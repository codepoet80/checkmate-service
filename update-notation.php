<?php
include("common.php");

$auth = get_authorization();

//Make sure we can get the input
$postjson = file_get_contents('php://input'); 
$postdata = json_decode($postjson); 

//Make sure the file exists and can be loaded
$file = get_filename_from_move($auth['move']);
$updatedtaskdata = "";
if (is_array($postdata)) {
    foreach ($postdata as $thistask) {
        $jsondata = get_notation_data($file, $auth['grandmaster']);
        $updatedtaskdata = update_or_create_task($thistask, $jsondata);
        file_put_contents($file, json_encode($updatedtaskdata, JSON_PRETTY_PRINT));
    }
}
if (is_object($postdata)) {
    $jsondata = get_notation_data($file, $auth['grandmaster']);
    $updatedtaskdata = update_or_create_task($postdata, $jsondata);
    file_put_contents($file, json_encode($updatedtaskdata, JSON_PRETTY_PRINT));
}

//Output the results
header('Content-Type: application/json');

$movedata = convert_move_to_public_schema($updatedtaskdata);
print_r (json_encode($movedata));
exit();

//Determine if this is a create or update
function update_or_create_task($newtaskitem, $oldtaskdata){
    $newtaskitem = validate_incoming_data($newtaskitem);
    $updatedtaskdata = "";
    if(strtolower($newtaskitem->guid) == "new")
    {
        $updatedtaskdata = create_new_task($newtaskitem, $oldtaskdata, false);
    }
    else
    {
        $existingtasks = $oldtaskdata['moves'];
        $found = false;
        foreach ($existingtasks as $existingtask)
        {
            if ($existingtask['guid'] == $newtaskitem->guid)
            {
                $found = true;
            }
        }

        if (!$found && $new) {  
            $updatedtaskdata = create_new_task($newtaskitem, $oldtaskdata, false);
        } else {
            $updatedtaskdata = update_existing_task($newtaskitem, $oldtaskdata);
        }
    }

    return $updatedtaskdata;
}

//Update (or delete) an existing task with new data
function update_existing_task($newtaskitem, $oldtaskdata){
    $updatedtasks = array();
    $existingtasks = $oldtaskdata['moves'];
    foreach ($existingtasks as $existingtask)
    {
        //if this task is NOT the task being edited, copy it to the new array as is
        if ($existingtask['guid'] != $newtaskitem->guid)
        {
            array_push($updatedtasks, $existingtask);
        }
    }
    //update the old array to be equal to the new array
    $oldtaskdata['moves'] = $updatedtasks;
    //then add the item being edited into the newly updated array IF this wasn't a delete
    if ($newtaskitem->sortPosition > -1)
        return create_new_task($newtaskitem, $oldtaskdata, true);
    else   //otherwise just return the newly updated array with that item missing
        return ($oldtaskdata);
}

//(Re)create a task, optionally include old data
function create_new_task($newtaskdata, $oldtaskdata, $resusedata){
    $updatedtaskdata = $oldtaskdata;
    $newtaskdata = (array)$newtaskdata;

    if (!$resusedata) {
        $newtaskdata['guid'] = uniqid();
        $newtaskdata['sortPosition'] = find_next_sortPosition($updatedtaskdata);
    }

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
    //TODO: validate new data before pushing
    return $newtaskdata;
}
?>