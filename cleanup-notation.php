<?php
include("common.php");
$visitorIP = get_visitor_ip();

$auth = get_authorization();
if (!isset($_SERVER['HTTP_USER_AGENT']) || empty($_SERVER['HTTP_USER_AGENT'])) {
    error_log("Check Mate Error: Anomalous cleanup call from " . $visitorIP . " with auth: " . json_encode($auth));
    die ("{\"error\":\"this is an anomalous cleanup call, and will be rejected and logged.\"}");
} else {
    error_log("Check Mate Trace: Allowed cleanup call from " . $visitorIP . " on " . $_SERVER['HTTP_USER_AGENT'] . " with auth: " . json_encode($auth));
}

//Make sure the file exists and can be loaded
$file = get_filename_from_move($auth['move']);
$jsondata = get_notation_data($file, $auth['grandmaster']);
//Write changes to file
$updatedtaskdata = remove_completed_tasks($jsondata);
$written = file_put_contents($file, json_encode($updatedtaskdata, JSON_PRETTY_PRINT));

//Output the results
header('Content-Type: application/json');
if (!$written) {
    echo "{\"error\":\"failed to write to file\"}";
} else {
    $movedata = convert_move_to_public_schema($updatedtaskdata);
    print_r (json_encode($movedata));    
}
exit();

//Update an existing task with new data
function remove_completed_tasks($oldtaskdata){
    $updatedtasks = array();
    $existingtasks = $oldtaskdata['moves'];
    foreach ($existingtasks as $existingtask)
    {
        if (!isset($existingtask['completed']) || $existingtask['completed'] != true)
        {
            array_push($updatedtasks, $existingtask);
        }
    }
    $oldtaskdata['moves'] = $updatedtasks;
    return $oldtaskdata;
}

?>