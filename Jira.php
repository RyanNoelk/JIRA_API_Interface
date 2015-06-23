<?php
/**
 * Created by PhpStorm.
 * User: Ryan Noelk
 * Date: 6/9/2015
*/

// Stores standard PHP PDO MySQL connection information
require_once "../config.php";
Global $conn;

// Include the classes (only once)
include_once('JiraIssues.class.php');
include_once('JiraDB.class.php');
include_once('JiraLogger.class.php');

// Create new log and give the filename and path to the log file
// If a log file is not need just remove the filename and log will be printed to the terminal
$log = new \JIRA\JiraLogger("tmp.txt");

//Loop though each JIRA Project that needs to be tracked
$query = "SELECT * FROM JIRA_MINER WHERE ACTIVE = TRUE;";
foreach ($conn->query($query) as $row)
{
    // Set and Initialize vars
    $id = $row['JIRA_MINER_ID'];
    $search = array();
    $jql = array();
    $max = 100; $start = 0;

    // Fetch all the filters for the query to JIRA
    $filter_query = "SELECT * FROM JIRA_FILTERS WHERE JIRA_MINER_ID = $id;";
    foreach ($conn->query($filter_query) as $filter_row)
        $jql[$filter_row['JIRA_COLUMN']] = $filter_row['FILTER'];

    // Fetch all the values to be returned from the query to JIRA
    $search_query = "SELECT * FROM JIRA_SEARCH WHERE JIRA_MINER_ID = $id;";
    foreach ($conn->query($search_query) as $search_row)
        $search[$search_row['JIRA_COLUMN']] = $search_row['DB_COLUMN'];

    // Print the above values to the log
    $log->print_header("DB Vars:");
    $log->print_line(print_r($jql, true));
    $log->print_line(print_r($search, true));

    $jira = new \JIRA\JiraIssues(
        $row['BASE_URL'],
        $jql,
        $row['CREDENTIALS'],
        $row['PROJECT'],
        $search,
        $row['MAX']);

    while (is_array($data = $jira->GetJIRAData($start, $max)) or $start == 0)
    {
        // Print the results to the log
        $log->print_header("URL:");
        $log->print_line($jira->current_url_string);
        $log->print_header("Raw Data:");
        $log->print_line(print_r($data, true));

        // Process the raw data (add it to the array)
        $jira->ProcessData($data);

        // Increase the start by the max and fetch again
        $start += $max;
    }

    // Print the Process defect array to the Log if its not null
    if ($jira->process_array)
    {
        $log->print_header("Formatted Data:");
        $log->print_line(print_r($jira->process_array, true));
    }

    // Add the processed data to the database
    \JIRA\JiraDB::UpdateDatabase(
        $jira->process_array,
        $row['SAVE_TABLE'],
        $row['SAVE_TABLE_PK'],
        $row['DOMAIN'],
        $row['PROJECT']);
}
