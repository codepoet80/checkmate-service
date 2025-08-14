<?php
include("common.php");
$visitorIP = get_visitor_ip();

$auth = get_authorization();
if (!isset($_SERVER['HTTP_USER_AGENT']) || empty($_SERVER['HTTP_USER_AGENT'])) {
    error_log("Check Mate Error: Anomalous update call from " . $visitorIP . " with auth: " . json_encode($auth));
    die ("{\"error\":\"this is an anomalous update call, and will be rejected and logged. User agent was " . $_SERVER['HTTP_USER_AGENT'] ."\"}");
}
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
        $written = file_put_contents($file, json_encode($updatedtaskdata, JSON_PRETTY_PRINT));
    }
}
if (is_object($postdata)) {
    $jsondata = get_notation_data($file, $auth['grandmaster']);
    $updatedtaskdata = update_or_create_task($postdata, $jsondata);
    $written = file_put_contents($file, json_encode($updatedtaskdata, JSON_PRETTY_PRINT));
}

//Output the results
header('Content-Type: application/json');
if (!$written) {
    die ("{\"error\":\"failed to write to file\"}");
} else {
    $movedata = convert_move_to_public_schema($updatedtaskdata);
    print_r (json_encode($movedata));    
}
exit();

//Determine if this is a create or update
function update_or_create_task($newtaskitem, $oldtaskdata){
    $newtaskitem = validate_incoming_data($newtaskitem);
    if (!$newtaskitem) {
        die ("{\"error\":\"incoming task data could not be validated: " . json_encode($newtaskitem) . "\"}");
    }
    else {
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
    if (isset($newtaskdata->guid) && isset($newtaskdata->title) && isset($newtaskdata->notes)) {
        $cleanedTask = new stdClass();
        
        // Enhanced validation for GUID
        $guid = strip_tags(trim($newtaskdata->guid));
        if ($guid === "new" || (strlen($guid) >= 6 && strlen($guid) <= 50 && preg_match('/^[a-zA-Z0-9_-]+$/', $guid))) {
            $cleanedTask->guid = $guid;
        } else {
            return false;
        }
        
        // Enhanced validation for title
        $title = strip_tags(trim($newtaskdata->title));
        if (strlen($title) > 0 && strlen($title) <= 200) {
            $cleanedTask->title = htmlspecialchars($title, ENT_QUOTES, 'UTF-8');
        } else {
            return false;
        }
        
        // Enhanced validation for notes
        $notes = strip_tags(trim($newtaskdata->notes));
        if (strlen($notes) <= 1000) {
            $cleanedTask->notes = htmlspecialchars($notes, ENT_QUOTES, 'UTF-8');
        } else {
            return false;
        }
        
        // Validate completed field
        if (isset($newtaskdata->completed)) {
            $cleanedTask->completed = filter_var($newtaskdata->completed, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
            if ($cleanedTask->completed === null) {
                $cleanedTask->completed = false;
            }
        }
        
        // Validate timestamps
        if (isset($newtaskdata->createTime)) {
            $createTime = strip_tags(trim($newtaskdata->createTime));
            if (strlen($createTime) <= 50 && preg_match('/^[a-zA-Z0-9\s,:-]+$/', $createTime)) {
                $cleanedTask->createTime = $createTime;
            }
        }
        
        if (isset($newtaskdata->completeTime)) {
            $completeTime = strip_tags(trim($newtaskdata->completeTime));
            if (strlen($completeTime) <= 50 && preg_match('/^[a-zA-Z0-9\s,:-]*$/', $completeTime)) {
                $cleanedTask->completeTime = $completeTime;
            }
        }
        
        // Validate sort position
        if (isset($newtaskdata->sortPosition)) {
            $sortPos = filter_var($newtaskdata->sortPosition, FILTER_VALIDATE_INT, array(
                "options" => array("min_range" => -1, "max_range" => 10000)
            ));
            if ($sortPos !== false) {
                $cleanedTask->sortPosition = $sortPos;
            } else {
                return false;
            }
        }
        
        return $cleanedTask;
    }
    else {
        return false;
    }
}
?>