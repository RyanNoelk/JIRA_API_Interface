<?php
/**
 * Created by PhpStorm.
 * User: Ryan Noelk
 * Date: 6/26/2015
 *
 * 1 test case for the Issues class:
 *      testBasicUrlConstructor - tests basic setup of a Jira issue object
 *
 * More test cases to come!
 */

define('__SCRIPT_ROOT', dirname(__FILE__));
require_once(__SCRIPT_ROOT . "/../JIRA/JiraIssues.class.php");


class JiraIssues_test extends PHPUnit_Framework_TestCase
{
    protected $base_url;
    protected $jql;
    protected $credentials;
    protected $project;
    protected $search_array;
    protected $days;

    protected function setUp()
    {
        $this->base_url = "https://jira.test.com";
        $this->jql = array("project" => "10825");
        $this->credentials = "username:password";
        $this->project = "test";
        $this->search_array = array("project->name" => "PROJECT",
            "priority->name" => "PRIORITY",
            "created" => "CREATED_DATE");
        $this->days = "2";
    }

    public function testBasicUrlConstructor()
    {
        $obj = new \JIRA\JiraIssues($this->base_url, $this->jql, $this->credentials, $this->project, $this->search_array, $this->days);

        $url = $this->base_url . "/rest/api/latest/search?jql=";
        $time = strtotime("-$this->days days");
        $url .= "updated>" . date('Y-m-d', $time);
        $url .= "%20AND%20project=10825";
        $url .= "&fields=id,project,priority,created";

        $this->assertEquals($this->credentials, $obj->credentials);
        $this->assertEquals($url , $obj->url_string);
        $this->assertEquals($this->project, $obj->project);
        $this->assertEquals($this->search_array, $obj->search_array);

    }
}
