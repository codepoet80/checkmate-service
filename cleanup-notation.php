<?php
include("common.php");

$auth = get_authorization();

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
        if ($existingtask['completed'] != true)
        {
            array_push($updatedtasks, $existingtask);
        }
    }
    $oldtaskdata['moves'] = $updatedtasks;
    return $oldtaskdata;
}

?>