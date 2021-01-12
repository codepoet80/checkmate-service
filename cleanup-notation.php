<?php
include("common.php");

$auth = get_authorization();

//Make sure the file exists and can be loaded
$file = get_filename_from_move($auth['move']);
$jsondata = get_notation_data($file, $auth['grandmaster']);

$updatedtaskdata = remove_completed_tasks($jsondata);

file_put_contents($file, json_encode($updatedtaskdata, JSON_PRETTY_PRINT));
header('Content-Type: application/json');
print_r (json_encode($updatedtaskdata));

//Update an existing task with new data
function remove_completed_tasks($oldtaskdata){
    $updatedtasks = array();
    $existingtasks = $oldtaskdata['moves'];
    foreach ($existingtasks as $existingtask)
    {
        if ($existingtask['completed'] != true)
        {
            array_push($updatedtasks, $existingtask);
        }
    }
    $oldtaskdata['moves'] = $updatedtasks;
    return $oldtaskdata;
}

?>