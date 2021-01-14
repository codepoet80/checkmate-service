<?php
include("common.php");
include("web-common.php");

//figure out paths
$readURL = get_function_endpoint("read-notation");
$postURL = get_function_endpoint("update-notation");

//add user's notation
$notationfile = "pawn-queensbishop4";
if (strpos($readURL, "?") === false) 
    $readURL .= "?";
else
    $readURL .= "&";
$readURL.="move=" . $notationfile;
$postURL.="?move=" . $notationfile;

//add user's password
$grandmaster = "Alexander Motylev";

//LOAD EXISTING DATA
$response = load_task_data($readURL, $notationFile, $grandmaster);
$data = json_decode($response);

//POST CHANGED DATA
if (isset($_GET["submit"]) && $_GET["submit"] == true)
{
    //Look for changed completion status
    $tasks = (array)$data->tasks;
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
        //echo "posted values: " . $_POST['editTaskID'] . $_POST['editTaskTitle'] . $_POST['editTaskNotes'];
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
    $response = update_task_data($postURL, $notationFile, $grandmaster, json_encode($tasks));
    $data = json_decode($response);
}

?>
<html>
<head>
<link rel="shortcut icon" href="favicon.ico">
<link rel="stylesheet" href="style.css">
<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=1" />

<title>Check Mate - Your To Do List Anywhere</title>
</head>
<body>
<h2><div><span>Check Mate - <?php echo $data->notation ?></span></div></h2>
<form action="?notation=<?php echo urlencode($data->notation)?>&submit=true" method="post">
<table cellpadding="2" cellspacing="2" border="0" width="80%">
<tr><td colspan="3"><hr></td></tr>
<?php
    $tasks = (array)$data->tasks;
    foreach ($tasks as $task)
    {
        echo "<tr><td><input type='checkbox' id='" . $task->guid . "' name='check[" . $task->guid . "]'";
        if ($task->completed)
            echo " checked";
        echo "><br/>&nbsp;</td>\r\n";
        echo "<td width='100%'><b>" . $task->title . "</b><br/>" . $task->notes . "<br/>&nbsp;</td>\r\n";
        echo "<td><a href='?edit=" . $task->guid . "'>Edit</a>";
        echo "<br><a href='?delete=" . $task->guid . "'> Delete</td></tr>\r\n";
    }
?>
<tr><td colspan="3"><hr></td></tr>
<?php

if (isset($_GET['edit']) && $_GET['edit'] != ""){
    $editGUID = $_GET['edit'];
    foreach ($tasks as $task)
    {
        if ($task->guid == $editGUID) {
            $editTask = $task;
        }
    }
    if (isset($editTask)) {     //never do HTML like this, is worse than the rest of this spaghetti code
        ?>
        <tr><td>Edit</td>
        <td><input type="text" name="editTaskTitle" id="editTaskTitle" value="<?php echo $editTask->title ?>">&nbsp;<br>
        <textarea name="editTaskNotes" id="editTaskNotes"><?php echo $editTask->notes ?></textarea>
        <input type="hidden" name="editTaskID" value="<?php echo $editGUID?>">
        </td></tr>
        <?php
    }
}
if (!isset($editTask)) {  
    ?>
    <tr><td>New</td>
    <td><input type="text" name="editTaskTitle" id="editTaskTitle">&nbsp;<br>
    <textarea name="editTaskNotes" id="editTaskNotes"></textarea>
    <input type="hidden" name="editTaskID" value="new">
    </td></tr>
    <?php
}
?>
<tr><td colspan="3" align="right"><input type="submit" value="Save Changes"></td></tr>
</table>
</form>
</body>
</html>