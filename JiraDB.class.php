<?php
/**
 * Created by PhpStorm.
 * User: v610091
 * Date: 6/11/2015
 * Time: 9:51 AM
 */

namespace JIRA;
require_once "../config.php";

class JiraDB {

    public static function UpdateDatabase($array, $table, $pk, $domain, $project)
    {
        foreach ($array as $map)
        {
            $query = "SELECT *
                   FROM $table
                   WHERE $pk = '$map[$pk]' AND
                         QC_DOMAIN = '$domain' AND
                         QC_PROJECT = '$project';";
            $row = mysql_fetch_array(mysql_query($query));
            if (!$row)
                self::CreateEntry($map, $table);
            else if (true /*need to compare time stamps here to see if we need to update*/)
                self::UpdateEntry($map, $table, $pk, $row[$pk]);
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