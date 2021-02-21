<?php
//figure out query
if (isset($_POST['move']) || isset($_GET['move']))  //find the move
{
    if (isset($_POST['move']))
        $move = try_make_move_from_input($_POST['move']);
    if (isset($_GET['move']))
        $move = try_make_move_from_input($_GET['move']);
}
if (isset($_POST['grandmaster']) || isset($_GET['grandmaster'])) //find the grandmaster
{  
    if (isset($_POST['grandmaster']))
        $grandmaster = $_POST['grandmaster'];
    if (isset($_GET['grandmaster']))
        $grandmaster = base64url_decode($_GET['grandmaster']);
    setcookie("grandmaster", $grandmaster, time() + (3600), "/");
}
else //or get from a cookie
{
    if (isset($_COOKIE["grandmaster"]))
    {
        $grandmaster = $_COOKIE["grandmaster"];
    }
} 
if (!isset($move) || $move == "" || !isset($grandmaster))  //or just go home if there's no valid query
{
    header ("Location: index.php");
    exit();
}

//figure out paths
$actionUrl = get_function_endpoint("tasks");
$actionUrl .= "?move=" . $move;
if (isset($_POST['useGet']) || isset($_GET['useGet'])) {
    $actionUrl .= "&useGet=true&grandmaster=" . base64url_encode($grandmaster);
    if (!isset($_GET['move'])) {
        header ("Location: " . $actionUrl);
        exit();
    }
}
$readURL = get_function_endpoint("read-notation");
$readURL.="?move=" . $move;
$postURL = get_function_endpoint("update-notation");
$postURL.="?move=" . $move;

//if we were loaded with a CLEANUP query, do that first
if (isset($_GET['cleanup']) && $_GET['cleanup'] == "complete")
{
    $cleanupURL = get_function_endpoint("cleanup-notation") . "?move=" . $move;
    $response = clear_completed_tasks($cleanupURL, $grandmaster);
    if (isset($response)) {
        if (check_response_for_errors($response)) {
            $data = json_decode($response);
        }
    }
} else //load existing data
{
    $response = load_task_data($readURL, $grandmaster);
    //check for errors
    if (isset($response)) {
        if (check_response_for_errors($response)) {
            $data = json_decode($response);
        }
    }
}

//if we were loaded with a DELETE query
if (isset($data) && (isset($_GET['delete']) && $_GET['delete'] != ""))
{
    //Just the tasks
    $tasks = (array)$data->tasks;
    //Figure out which one to delete
    $delGUID = $_GET['delete'];
    foreach ($tasks as $task)
    {
        if ($task->guid == $delGUID) {
            $task->sortPosition = -1;
        }
    }
    $response = update_task_data($postURL, $grandmaster, json_encode($tasks));
    if (isset($response)) {
        if (check_response_for_errors($response)) {
            $data = json_decode($response);
        }
    }
}

//if we were loaded with any other kinds of data updates
if (isset($data) && (isset($_GET["dosubmit"]) && $_GET["dosubmit"] == true) || (isset($_POST["dosubmit"]) && $_POST["dosubmit"] == true))
{
    //Just the tasks
    $tasks = (array)$data->tasks;
    //Look for changed completion status
    foreach ($tasks as $task) {
        if ($task->completed) { //If this was a completed task, check if its been un-completed
            if (isset($_POST['check'])) {
                $found = false;
                foreach($_POST['check'] as $index => $value) {
                    if ($index == $task->guid)
                    {
                        $found = true;
                    }
                }
            }
            if (!$found)
                $task->completed = false;
        }
        else {  //If this was an incomplete task, check if its been completed
            if (isset($_POST['check'])) {
                foreach($_POST['check'] as $index => $value) {
                    if ($index == $task->guid)
                    {
                        $task->completed = true;
                    }
                }
            }
        }
    }
    //Look for new or edited tasks
    if (isset($_POST['editTaskTitle']) && $_POST['editTaskTitle'] != '')
    {
        $taskGUID = $_POST['editTaskID'];
        if ($taskGUID == "new"){
            //form and post a new task, reload tasks
            $newtask = new stdClass();
            $newtask->guid = "new";
            $newtask->title = $_POST['editTaskTitle'];
            $newtask->notes = $_POST['editTaskNotes'];
            array_push($tasks, $newtask);
        } else {
            //edit an existing task (to be posted later)
            foreach ($tasks as $task)
            {
                if ($task->guid == $taskGUID) {
                    $task->title = $_POST['editTaskTitle'];
                    $task->notes = $_POST['editTaskNotes'];
                }
            }
        }
    }
    //post changes and get updated data
    $response = update_task_data($postURL, $grandmaster, json_encode($tasks));

    if (isset($response)) {
        if (check_response_for_errors($response)) {
            $data = json_decode($response);
        }
    }
    //$debugMsg .= "Data now: <br>";
    //$debugMsg .= $response;
}

//error handling for all loads
function check_response_for_errors($response)
{
    if (strpos(strtolower($response), "{\"error\":\"failed to write to file\"}")) {
        $GLOBALS['debugMsg'] .= "The notation file could not be written.<br>";
        $GLOBALS['debugMsg'] .= "Administrator: ensure your notations folder and all contents are writeable by the web server user.";
        return false;
    }
    try {
        $data = json_decode($response);
    }
    catch (exception $e) {
        $GLOBALS['debugMsg'] .= "An error occurred parsing your move file: <br>";
        $GLOBALS['debugMsg'] .= $e->getMessage();
        return false;
    }
    
    if (isset($data->error))
    {
        if (strpos(strtolower($data->error), "illegal move") !== false)
        {
            //Wrong password
            $GLOBALS['debugMsg'] .= "Authentication failure: <br>";
            $GLOBALS['debugMsg'] .= $data->error;
            $GLOBALS['debugMsg'] .= "<br><a href='index.php?login=fail'>Return to Log In</a>";
            header ("Location: index.php?login=fail");
            exit();
        }
        else
        {
            //Some other error
            $GLOBALS['debugMsg'] .= "An error occurred opening your data file: <br>";
            $GLOBALS['debugMsg'] .= $data->error;
            return false;
        }
    }
    return true;
}
?>