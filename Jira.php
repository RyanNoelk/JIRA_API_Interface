<?php
/**
 * Created by PhpStorm.
 * User: v610091
 * Date: 6/9/2015
* Time: 11:38 AM
*/

include_once('JiraData.class.php');
include_once('JiraDB.class.php');

$domain = 'JIRA';
$project = 'JIRA_PROJECT';
$table = 'QC_DEFECT_DATA';
$pk = 'QC_DEFECT_ID';
$credentials = 'username:password';
$base_url = 'http://jira.com';
$jql = array('project'=>'10825');
$search = array('project_name'=>'QC_ACTUAL_PROJECT','priority_name'=>'QC_PRIORITY','duedate'=>'QC_RELEASE_TO_TEST_DATE');

$jira = new \JIRA\JiraData($base_url, $jql, $credentials, $project, $search);

$jira->GetJIRAData();
$jira->ProcessData();
print_r($jira->process_array);

\JIRA\JiraDB::UpdateDatabase($jira->process_array, $table, $pk, $domain, $project);
