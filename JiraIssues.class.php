<?php
/**
 * Created by PhpStorm.
 * User: Ryan Noelk
 * Date: 6/9/2015
 *
 * This class will assist in the logging process for JIRA queries
 * There are two ways to record the log:
 *      1. To the command line
 *      2. To a specific path/file
 * The constructor takes 5 parameters:
 *      1. base_url ->
 *      2. jql -> array of filters for the JIRA query
 *      3. credentials -> username:password
 *      4. project -> JIRA project key
 *      5. search_array -> array of return values from the JIRA query
 * Public Functions:
 *      1. GetJIRAData -> Query JIRA with given URL for data with a specific start and max
 *      2. ProcessData -> Format the JIRA raw data into a formatted data
 */

namespace JIRA;


class JiraIssues {

    public $url_string = ''; // string which contains the URL to be sent to JIRA
    public $current_url_string = ''; // current string which contains the URL to be sent to JIRA
    public $credentials = ''; // username:password
    public $project = ''; // JIRA project to mine data from
    public $search_array = ''; // database array map
    public $process_array = array(); // Well formatted data ready to be inserted into the DB

    function __construct($base_url, $jql, $credentials, $project, $search_array, $days)
    {
        $this->project = $project;
        $this->credentials = $credentials;
        $this->search_array = $search_array;
        $this->process_array = array();

        // build the URL
        $this->url_string = $base_url."/rest/api/latest/search?jql=";

        $time = strtotime("-$days days");
        $this->url_string .= "updated>" . date('Y-m-d', $time);
        foreach ($jql as $key=>$value)
            $this->url_string .= "%20AND%20" . $key . "=" . $value;

        $this->url_string .= "&fields=id";
        foreach ($search_array as $key=>$value)
        {
            $str = explode('->', $key);
            $this->url_string .= ",".$str[0];
        }
    }

    // Query JIRA with given URL for data with a specific start and max
    public function GetJIRAData($start_at, $max_results)
    {
        // Add the start and max to the query
        $this->current_url_string = $this->url_string . "&startAt=$start_at&maxResults=$max_results";

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
        curl_setopt($qc, CURLOPT_URL, $this->current_url_string);
        curl_setopt($qc, CURLOPT_RETURNTRANSFER, true);

        // Execute query
        $xml = curl_exec($qc);

        // Return raw data
        $decoded = json_decode($xml, true);

        if ('' == $decoded)
            return $xml;
        else if (empty ($decoded['issues']))
            return "No Data";
        else
            return $decoded;

    }

    // Format the JIRA raw data into a formatted data
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
                {
                    if (is_array($field[0]))
                        self::getDataFromArray($map, $key, $field[0]);
                    else
                        self::getDataFromArray($map, $key, $field);
                }
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

    // Decode a json array within JIRA
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