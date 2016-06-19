<?php

/**
 * @Author: sahildua2305
 * @Date:   2016-06-19 19:58:20
 * @Last Modified by:   sahildua2305
 * @Last Modified time: 2016-06-20 01:51:17
 */

/**
 * Set headers to `application/json` so that the output is rendered
 * correctly on Slack
 */
header('Content-Type: application/json');

/**
 * Include config variables to validate the request received
 */
include 'slack-secrets.php';

/**
 * Validate request by matching `team_id` and `token` received from Slack
 */
if((!isset($_POST['team_id']) || $_POST['team_id'] !== $TEAM_ID) ||
    (!isset($_POST['token']) || $_POST['token'] !== $TOKEN)) {
    die("Invalid request");
    echo "Invalid request";
}

/**
 * Text that is to be searched
 * Whatever user types after the matching slash command, comes here
 *
 * For example, if `/ddg` is the slash command for your this integration
 * and user enters `/ddg url encode`, `url encode` will go into this variable
 * @var string
 */
$text = $_POST['text'];

/**
 * URL of the file containing the Instant Answers json data
 * as fetched from https://duck.co/ia/ website's source code
 * @var string
 */
$url = "ia-data.json";

/**
 * Load the contents of `ia-data.json` in a variable
 * @var string
 */
$str = file_get_contents($url);

/**
 * Decode the json encoded string to associative array
 * @var array
 */
$ia_list = json_decode($str, true);

/**
 * Initialize the array which will store the information about
 * Instant Answers which match the query
 * @var array
 */
$filtered_list = array();

/**
 * Iterate over the entire list of Instant Answers fetched from `ia-data.json`
 */
foreach($ia_list as $ia) {

    $description = $ia["description"];
    $name = $ia["name"];

    /**
     * Search for given text in both name as well as description
     * of the Instant Answer
     */
    if((strpos($name, $text) !== false) ||
        (strpos($description, $text) !== false) ||
        (strpos($name, strtoupper($text)) !== false) ||
        (strpos($description, strtoupper($text)) !== false)) {

        $temp = array();
        $temp["name"] = $name;
        $temp["description"] = $description;
        $temp["id"] = $ia["id"];
        array_push($filtered_list, $temp);

    }
}

/**
 * Concatenation of information of all Instant Answers matching the query
 * @var string
 */
$output = "";

/**
 * Iterate over the filtered list of Instant Answers
 * and concatenate all the links
 */
foreach($filtered_list as $ia) {
    $output .= '<https://duck.co/ia/view/' . $ia["id"] . '|' . $ia["name"] . '>';
    $output .= "\n";
}

/**
 * Array that will be returned as a response to be rendered by Slack
 * @var array
 */
$response = array();

/**
 * If search results doesn't contain even a single Instant Answer,
 * show the corresponding message
 */
if(strlen($output) == 0){
    $response["text"] = ":interrobang: Sorry! No results found.";
}
else{
    $response["text"] = "This is how search results look like for " . $text;

    /**
     * `attachments` consists of list of all attachments to be returned
     * We will be sending all links in one attachment only
     */
    $response["attachments"] = array(array());

    /**
     * Add `title`, `title_link`, `color` and `text` to the array
     * to be returned as a lone attachment
     * @var array
     */
    $r = array();
    $r["title"] = "DuckDuckGo Search Results";
    $r["title_link"] = "https://duckduckgo.com/?q=" . urlencode($text);
    $r["color"] = "#DE5833";
    $r["text"] = $output;

    /**
     * Add the associative array to the attachments
     */
    $response["attachments"][0] = $r;
}

/**
 * Encode the response in json form and return
 */
echo json_encode($response);
