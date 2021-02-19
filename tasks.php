<?php
include("common.php");
include("web-functions.php");

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
<html>
<head>
<link rel="shortcut icon" sizes="256x256" href="images/icon-256.png">
<link rel="shortcut icon" sizes="196x196" href="images/icon-196.png">
<link rel="shortcut icon" sizes="128x128" href="images/icon-128.png">
<link rel="shortcut icon" href="favicon.ico">
<link rel="icon"type="image/png" href="images/icon.png" >
<link rel="apple-touch-icon" href="images/icon.png"/>
<link rel="apple-touch-startup-image" href="images/icon-256.png">
<meta name="apple-mobile-web-app-capable" content="yes" />
<meta name="apple-mobile-web-app-status-bar-style" content="white" />

<link rel="stylesheet" href="style.css">
<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=1" />
<meta http-equiv="pragma" content="no-cache">
<title>Check Mate - Your To Do List Anywhere</title>
<script>
    function swapTech()
    {
        document.getElementById("imgIcon").src = "images/icon.png";
        document.getElementById("tableControls").style.marginTop = "14px";
        document.getElementById("divCancel").innerHTML = "<input type=\"button\" value=\"Cancel Changes\" class=\"button\" onclick=\"document.location='<?php echo $actionUrl?>'\"/>";
        document.getElementById("divLogout").innerHTML = "<input type=\"button\" value=\"Log Out\" class=\"button\" onclick=\"document.location='index.php'\"/>";
        document.getElementById("divCleanup").innerHTML = "<input type=\"button\" value=\"Cleanup\" class=\"button\" onclick=\"document.location='<?php echo $actionUrl?>&cleanup=complete'\"/>";
        //TODO: 
        // Replace delete link with button
        // If !XMLHTTPRequest
            //Replace edit link with button
        // Else
            //Replace edit fields with double click to edit
    }

    function checkTask(checkbox)
    {
        if (checkbox.checked) {
            var audio = new Audio('sounds/completed1.mp3');
        } else {
            var audio = new Audio('sounds/flick1.mp3');
        }
        audio.play();
        var theForm = document.getElementById('formTasks');
        theForm.submit();
    }
    
</script>
</head>
<body onload="swapTech()">
<table width="80%" class="contentTable">
    <tr>
        <td><img src="images/icon.gif" id="imgIcon"></td>
        <td width="100%"><h2><div><span>Check Mate<br><i><?php echo $data->notation ?></i></span></div></h2></td>
        <td><div id="divLogout" style="float:right"><a href="index.php?logout=true">Log out</a></div></td>
    </tr>
</table>

<?php
    //Display debugMsg if any
    if ($debugMsg != "") {
        echo "<br><table width=\"400\" bgcolor=\"pink\" border=\"1\" class=\"tableLogin\" align=\"center\"><tr><td class=\"tableLogin\" >" . $debugMsg . "<br>";
        echo "<br>POST data was: ";
        print_r ($_POST);
        echo "</td></tr></table>";
        echo "<br>";
    }
?>
<form id="formTasks" name="formTasks" action="<?php echo $actionUrl?>" method="post">
    <!-- Main Tasks Table -->
    <table cellpadding="2" cellspacing="2" border="0" width="80%" class="contentTable">
        <tr><td colspan="3" id="taskTableFrameTop"><hr/></td></tr>
        <?php
        $tasks = (array)$data->tasks;
        foreach ($tasks as $task)
        {
            echo "\t\t<tr>\r\n";
            echo "\t\t\t<td><input type='checkbox' id='" . $task->guid . "' name='check[" . $task->guid . "]'";
            if ($task->completed)
                echo " checked";
            echo " onchange='checkTask(this)'/></td>\r\n";
            echo "\t\t\t<td valign='middle' width='100%' class='taskListDetailCell'><b>" . $task->title . "</b>";
            if ($task->notes != "") {
                echo "<br/>" . $task->notes . "<br/>";
            } 
            echo "</td>\r\n";
            echo "\t\t\t<td><a href=\"" . $actionUrl . "&edit=" . $task->guid . "#editfield\">Edit</a>\r\n";
            echo "\t\t\t\t<a href='" . $actionUrl . "&delete=" . $task->guid . "'>Delete</a></td>\r\n";
            echo "\t\t</tr>\r\n";
            echo "\t\t<tr><td colspan='3'><img src='images/spacer.gif' height='4'/></td></tr>\r\n";
        }
        ?>
        <tr><td colspan="3" id="taskTableFrameBottom"><hr></td></tr>
    </table>
    
    <?php
    //Figure out what to put in New/Edit area for older clients
    if (isset($_GET['edit']) && $_GET['edit'] != ""){
        $editGUID = $_GET['edit'];
        foreach ($tasks as $task)
        {
            if ($task->guid == $editGUID) {
                $editTask = $task;
            }
        }
    }
    if (isset($editTask)) {
        $editTitle = "Edit Task";
    }
    else {
        $editTitle = "New Task";
        $editTask = new stdClass();
        $editTask->title = "";
        $editTask->notes = "";
        $editGUID = "new";
    }
    ?>

    <!-- New/Edit Area for older clients -->
    <table id="tableEdit" width="80%" cellpadding="0" cellspacing="0" border="0" class="contentTable">
    <tr>
        <td colspan="2">
            <span class="editTitle"><a name="editfield"><i><b><?php echo $editTitle ?></b></i></a></span>
            <br/>&nbsp;
        </td>
    <tr>
        <td valign="top" width="90">
            &nbsp;Task Title: &nbsp;
        </td>
        <td width="*">
            <input type="text" size="45" name="editTaskTitle" id="editTaskTitle" value="<?php echo $editTask->title ?>"/>
        </td>
    </tr>
    <tr>
        <td valign="top" width="90">
            &nbsp;Task Notes: &nbsp;
        </td>
        <td width="*">
            <textarea name="editTaskNotes" cols="40" rows="5" id="editTaskNotes"><?php echo $editTask->notes ?></textarea>
        </td>
    </tr>
    </table>
    <img src="images/spacer.gif" height="4">

    <!-- Control Buttons -->
    <table id="tableControls" width="80%" cellpadding="0" cellspacing="0" border="0" class="contentTable">
    <tr>
        <td align="left">
            <span id="divCancel"><a href="<?php echo $actionUrl ?>">Cancel Changes</a></span>
        </td>
        <td align="right">
            <input type="hidden" name="editTaskID" value="<?php echo $editGUID?>">
            <span id="divCleanup"><a href="<?php echo $actionUrl ?>?cleanup=complete">Clean-up</a></span>&nbsp;
            <input type="submit" value="Save Changes" class="button">
        </td>
    </tr>
    </table>
    <input type="hidden" name="dosubmit" value="on"/>
    
</form>
</body>
</html>