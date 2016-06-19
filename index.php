<?php

/**
 * @Author: sahildua2305
 * @Date:   2016-06-19 19:58:20
 * @Last Modified by:   sahildua2305
 * @Last Modified time: 2016-06-20 00:12:31
 */

header('Content-Type: application/json');

include 'config/slack-secrets.php';

// Verification
if((!isset($_POST['team_id']) || $_POST['team_id'] !== $TEAM_ID) ||
    (!isset($_POST['token']) || $_POST['token'] !== $TOKEN)) {
    // die("Invalid request");
    // echo "Invalid request";
}

// $command = $_POST['command'];
// $text = $_POST['text'];
// $token = $_POST['token'];

$text = "url";
$url = "ia-data.json";

$str = file_get_contents($url);
$json = json_decode($str, true);
print_r($json);

$response = array();
$response["text"] = "This is how search results look like for " . $text;
$response["attachments"] = array(array());

$r = array();
$r["title"] = "DuckDuckGo Search Results";
$r["title_link"] = "https://duckduckgo.com/?q=" . urlencode($text);
$r["image_url"] = "http://sahildua.com/img/sahildua.jpg";

$response["attachments"][0] = $r;

echo json_encode($response);
