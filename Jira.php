<?php
/**
 * Created by PhpStorm.
 * User: v610091
 * Date: 6/9/2015
* Time: 11:38 AM
*/

// Stores standard PHP PDO MySQL connection information
require_once "../config.php";
Global $conn;

include_once('JiraIssues.class.php');
include_once('JiraDB.class.php');


$query = "SELECT * FROM JIRA_MINER WHERE ACTIVE = TRUE;";
foreach ($conn->query($query) as $row)
{
    $id = $row['JIRA_MINER_ID'];
    $search = array();
    $jql = array();

    $filter_query = "SELECT * FROM JIRA_FILTERS WHERE JIRA_MINER_ID = $id;";
    foreach ($conn->query($filter_query) as $filter_row)
        $jql[$filter_row['JIRA_COLUMN']] = $filter_row['FILTER'];

    $search_query = "SELECT * FROM JIRA_SEARCH WHERE JIRA_MINER_ID = $id;";
    foreach ($conn->query($search_query) as $search_row)
        $search[$search_row['JIRA_COLUMN']] = $search_row['DB_COLUMN'];

    /*print_r($jql);
    print_r($search);*/

    $jira = new \JIRA\JiraIssues(
        $row['BASE_URL'],
        $jql,
        $row['CREDENTIALS'],
        $row['PROJECT'],
        $search,
        $row['MAX']);

    $jira->ProcessData($jira->GetJIRAData(0));

    echo $jira->url_string;
    //print_r($jira->json_decoded_array);
    print_r($jira->process_array);

    \JIRA\JiraDB::UpdateDatabase(
        $jira->process_array,
        $row['SAVE_TABLE'],
        $row['SAVE_TABLE_PK'],
        $row['DOMAIN'],
        $row['PROJECT']);
}
