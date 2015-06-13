<?php
/**
 * Created by PhpStorm.
 * User: v610091
 * Date: 6/9/2015
 * Time: 11:24 AM
 */

namespace JIRA;


class JiraIssues {

    public $url_string = ''; // string which contains the URL to be sent to JIRA
    public $credentials = ''; // username:password
    public $project = ''; // JIRA project to mine data from
    public $max_results = ''; // Max number to issues to be returned in one query
    public $search_array = ''; // database array map
    public $json_decoded_array = ''; // json decoded data from JIRA query
    public $process_array = array(); // Well formatted data ready to be inserted into the DB

    function __construct($base_url, $jql, $credentials, $project, $search_array, $max_results)
    {
        $this->project = $project;
        $this->credentials = $credentials;
        $this->search_array = $search_array;
        $this->process_array = array();
        $this->max_results = 100;

        $this->url_string = $base_url."/rest/api/latest/search?jql=";
        foreach ($jql as $key=>$value)
            $this->url_string .= $key."=".$value;

        $this->url_string .= "&fields=id";
        foreach ($search_array as $key=>$value)
        {
            $str = explode('->', $key);
            $this->url_string .= ",".$str[0];
        }
    }

    public function GetJIRAData($start_at)
    {
        // Add the start and max to the query
        $url = $this->url_string . "&startAt=$start_at&maxResults=$this->max_results";

        // Create a new cURL resource
        $qc = curl_init();

        // Build the header with login
        $headers = array("GET /HTTP/1.1","Authorization: Basic ". base64_encode($this->credentials));

        // cURL settings
        curl_setopt($qc, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
        curl_setopt($qc, CURLOPT_HEADER, 0);
        curl_setopt($qc, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($qc, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($qc, CURLOPT_AUTOREFERER, 1);
        curl_setopt($qc, CURLOPT_HTTPGET,1);
        curl_setopt($qc, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($qc, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($qc, CURLOPT_URL, $url);
        curl_setopt($qc, CURLOPT_RETURNTRANSFER, true);

        // Execute query
        $xml = curl_exec($qc);
        $response = curl_getinfo($qc);

        // Return raw data
        echo $xml;
        $decoded = json_decode($xml, true);

        if ('' == $decoded)
            return "Error";
        else if ('' == $decoded['issues'])
            return "No Data";
        else
            return $decoded;

    }

    public function ProcessData($json_decoded_array)
    {
        foreach ($json_decoded_array['issues'] as $issue)
        {
            $map = array();
            foreach ($issue['fields'] as $key=>$field)
            {
                if (null == $field or '' == $field)
                    continue;
                else if (is_array($field))
                    self::getDataFromArray($map, $key, $field);
                else if ('' != $this->search_array[$key])
                    $map[$this->search_array[$key]] = $field;
            }

            $map['QC_DEFECT_ID'] = $issue['id'];
            $map['QC_DOMAIN'] = "JIRA";
            $map['QC_PROJECT'] = $this->project;
            $map['ERA_CREATED_DATE'] = 'NOW()';
            array_push($this->process_array, $map);
        }
    }


    private function getDataFromArray(&$map, $append, $field)
    {
        foreach ($field as $sub_key=>$sub_field)
        {
            $key = $append."->".$sub_key;
            if (null == $field or '' == $field)
                continue;
            else if (is_array($sub_field))
                self::getDataFromArray($map, $key, $sub_field);
            else if ('' != $this->search_array[$key])
                $map[$this->search_array[$key]] = $sub_field;
        }
    }


}