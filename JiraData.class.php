<?php
/**
 * Created by PhpStorm.
 * User: v610091
 * Date: 6/9/2015
 * Time: 11:24 AM
 */

namespace JIRA;


class JiraData {

    public $url_string = ''; // string which contains the URL to be sent to JIRA
    public $credentials = ''; // username:password
    public $project = ''; // JIRA project to mine data from
    public $search_array = ''; // database array map
    public $json_decoded_array = ''; // json decoded data from JIRA query
    public $process_array = array(); // Well formatted data ready to be inserted into the DB

    function __construct($base_url, $jql, $credentials, $project, $search_array)
    {
        $this->project = $project;
        $this->credentials = $credentials;
        $this->search_array = $search_array;
        $this->process_array = array();

        $this->url_string = $base_url."/rest/api/latest/search?jql=";
        foreach ($jql as $key=>$value)
            $this->url_string .= $key."=".$value;
        //$this->url_string .= "&fields=id,key,status,project";
    }

    public function GetJIRAData()
    {
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
        curl_setopt($qc, CURLOPT_URL, $this->url_string);
        curl_setopt($qc, CURLOPT_RETURNTRANSFER, true);

        // Execute query
        $xml = curl_exec($qc);
        $response = curl_getinfo($qc);

        // Return raw data
        $this->json_decoded_array = json_decode($xml, true);
    }

    public function ProcessData()
    {
        foreach ($this->json_decoded_array['issues'] as $issue)
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
            $key = $append."_".$sub_key;
            if (null == $field or '' == $field)
                continue;
            else if (is_array($sub_field))
                self::getDataFromArray($map, $key, $sub_field);
            else if ('' != $this->search_array[$key])
                $map[$this->search_array[$key]] = $sub_field;
        }
    }


}