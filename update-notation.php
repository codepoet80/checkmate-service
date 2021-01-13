<?php
include("common.php");

$auth = get_authorization();

//Make sure the file exists and can be loaded
$file = get_filename_from_move($auth['move']);
$jsondata = get_notation_data($file, $auth['grandmaster']);

$postjson = file_get_contents('php://input'); 
$postdata = json_decode($postjson); 
$updatedtaskdata = "";
if (is_array($postdata)) {
    foreach ($postdata as $thistask) {
        $updatedtaskdata = update_or_create_task($thistask, $jsondata);
    }
}
if (is_object($postdata)) {
    $updatedtaskdata = update_or_create_task($postdata, $jsondata);
}
if ($updatedtaskdata != "") {
    file_put_contents($file, json_encode($updatedtaskdata, JSON_PRETTY_PRINT));
    header('Content-Type: application/json');
    print_r (json_encode($updatedtaskdata));
} else {
    die ("Something went wrong updating data!");
}

//Determine if this is a create or update
function update_or_create_task($newtaskdata, $oldtaskdata){
    $newtaskdata = validate_incoming_data($newtaskdata);
    $updatedtaskdata = "";
    if(strtolower($newtaskdata->guid) == "new")
    {
        $updatedtaskdata = create_new_task($newtaskdata, $oldtaskdata);
    }
    else
    {
        $existingtasks = $oldtaskdata['moves'];
        $found = false;
        foreach ($existingtasks as $existingtask)
        {
            if ($existingtask['guid'] == $newtaskdata->guid)
            {
                $found = true;
            }
        }
        if (!$found) {
            $updatedtaskdata = create_new_task($newtaskdata, $oldtaskdata);
        } else {
            $updatedtaskdata = update_existing_task($newtaskdata, $oldtaskdata, $newtaskdata->guid);
        }
    }

    return $updatedtaskdata;
}

//Update an existing task with new data
function update_existing_task($newtaskdata, $oldtaskdata, $guid){
    $updatedtasks = array();
    $existingtasks = $oldtaskdata['moves'];
    foreach ($existingtasks as $existingtask)
    {
        //if this task is NOT the task being edited, copy it to the new array as is
        if ($existingtask['guid'] != $newtaskdata->$guid)
        {
            array_push($updatedtasks, $existingtask);
        }
    }
    //update the old array to be equal to the new array
    $oldtaskdata['moves'] = $updatedtasks;
    //then add the item being edited into the newly updated array IF this wasn't a delete
    if ($newtaskdata->sortPosition > -1)
        return create_new_task($newtaskdata, $oldtaskdata);
    else   //otherwise just return the newly updated array with that item missing
        return ($oldtaskdata);
}

//Create a brand new task
function create_new_task($newtaskdata, $oldtaskdata){
    $updatedtaskdata = $oldtaskdata;
    $newtaskdata = (array)$newtaskdata;
    $newtaskdata['sortPosition'] = find_next_sortPosition($updatedtaskdata); 
    $newtaskdata['guid'] = uniqid();
    array_push($updatedtaskdata['moves'], $newtaskdata);
    usort($updatedtaskdata['moves'], 'sorter');
    
    return $updatedtaskdata;
}

function sorter($object1, $object2) { 
    return $object1['sortPosition'] < $object2['sortPosition']; 
} 

//Figure out what the next highest sort position is in our existing task list
function find_next_sortPosition($oldtaskdata) {
    $newsortpos = 0;
    $existingtasks = $oldtaskdata['moves'];
    foreach ($existingtasks as $existingtask)
    {
        if ($existingtask['sortPosition'] > $newsortpos)
        {
            $newsortpos = $existingtask['sortPosition'];
        }
    }
    $newsortpos = $newsortpos + 1;
    return $newsortpos;
}

function validate_incoming_data($newtaskdata)
{
    //todo: validate new data before pushing
    return $newtaskdata;
}

//add or update notation json with post data, based on content

//header('Content-Type: application/json');
//print_r (json_encode($postdata));
?>