<?php
/**
 * Created by PhpStorm.
 * User: v610091
 * Date: 6/11/2015
 *
 * Custom Database Code used for mapping JIRA formatted data into a MySQL DB
 *
 *
 */

namespace JIRA;

// define the root path for including files. If you use this in a cron, it may not know where to look for the included/required files/classes.
define('__SCRIPT_ROOT', dirname(__FILE__));
require_once(__SCRIPT_ROOT . "/../../config.php");

class JiraDB {

    public static function UpdateDatabase($array, $table, $pk, $domain, $project)
    {
        Global $conn;
        foreach ($array as $map)
        {
            $stmt = $conn->prepare("SELECT *
                                    FROM $table
                                    WHERE $pk = :pk AND
                                          QC_DOMAIN = :domain AND
                                          QC_PROJECT = :project;");
            $row = $stmt->execute(array(":pk" => $map[$pk],":domain" => $domain,":project" => $project));
            if (!$row = $stmt->fetch())
                self::CreateEntry($map, $table);
            else if (true /*need to compare time stamps here to see if we need to update*/)
            {
                $tmp = $conn->prepare("show index from $table where Key_name = 'PRIMARY';");
                $tmp->execute();
                $pri_key = $tmp->fetch();
                self::UpdateEntry($map, $table, $pri_key['Column_name'], $row[$pri_key['Column_name']]);
            }
        }
    }

    private static function CreateEntry($map, $table)
    {
        $insert = $values = '';
        foreach ($map as $key => $value)
        {
            $esp_value = strip_tags(mysql_real_escape_string($value));
            if ($esp_value != '')
            {
                if ($insert == '')
                {
                    $insert = "$key";
                    $values = "'$esp_value'";
                }
                else
                {
                    $insert = $insert.", $key";
                    if ($esp_value == 'NOW()')
                        $values = $values.", $esp_value";
                    else
                        $values = $values.", '$esp_value'";
                }
            }
        }

        $query = "INSERT INTO $table ($insert)
                  VALUES ($values)";
        $result=mysql_query($query);
    }

    private static function UpdateEntry($map, $table, $pri_key, $pri_key_value)
    {
        $set = '';
        foreach ($map as $key => $value)
        {
            $esp_value = strip_tags(mysql_real_escape_string($value));
            if ($esp_value != '')
            {
                if ($set == '')
                {
                    $set = "$key = '$esp_value'";
                }
                else
                {
                    if ($esp_value == 'NOW()')
                        $set = $set.", $key = $esp_value";
                    else
                        $set = $set.", $key = '$esp_value'";
                }
            }
        }

        $query = "UPDATE $table
                  SET $set
                  WHERE $pri_key = '$pri_key_value'
                  LIMIT 1";
        $result=mysql_query($query);
    }


}