<?php
    include("common.php");
    include("web-functions.php");
    include("web-tasktable.php");
    include("task-functions.php");
?>
<html>
<head>
<?php
    include("web-meta.php");
?>
<script>
    var xhr = false;
    var taskModel = false;
    var updateInt;
</script>
<script type="text/javascript" src="checkmate-ajax.js?nocache=<?php echo uniqid(); ?>"></script>
<script type="text/javascript" src="task-model.js?nocache=<?php echo uniqid(); ?>"></script> 
<script type="text/javascript" src="web-dragdrop.js?nocache=<?php echo uniqid(); ?>"></script>
<script>
    var actionUrl = "<?php echo $actionUrl ?>";
    if (taskModel) {
        checkmate.actionUrl = actionUrl;
        taskModel.grandmaster = "<?php echo base64_encode ($grandmaster) ?>";
        taskModel.notation = "<?php echo $data->notation ?>";
        taskModel.taskData = JSON.parse("<?php echo addslashes(json_encode($data->tasks)); ?>");
        xhr = checkmate.detectXHR();
    }

    function swapTech() {
        document.getElementById("imgIcon").src = "images/icon.png";
        document.getElementById("tableControls").style.marginTop = "14px";
        document.getElementById("divLogout").innerHTML = "<input type=\"button\" value=\"Log Out\" class=\"button\" onclick=\"document.location='index.php'\"/>";
        if (taskModel) { // client needs to be able to load an external javascript
            taskModel.upgradeUX();
            clearInterval(updateInt);
            updateInt = setInterval("taskModel.doRefresh()", 10000)
        }
    }

    function doSave() {
        document.getElementById('formTasks').submit();
        //TODO: Detect AJAX and change
    }

</script>
</head>
<body onload="swapTech()">
<table class="contentTable" width="95%">
    <tr>
        <td><img src="images/icon.gif" id="imgIcon"></td>
        <td width="100%"><h2><div><span>Check Mate<br><i><?php echo $data->notation ?></i></span></div></h2></td>
        <td><div id="divLogout" style="float:right"><a href="index.php?logout=true">Log out</a></div></td>
    </tr>
</table>

<?php
    //echo $actionUrl;
    //Display debugMsg if any
    if (isset($debugMsg) && $debugMsg != "") {
        echo "<br><table width=\"400\" bgcolor=\"pink\" border=\"1\" class=\"tableLogin\" align=\"center\"><tr><td class=\"tableLogin\" >" . $debugMsg . "<br>";
        echo "<br>POST data was: ";
        print_r ($_POST);
        echo "</td></tr></table>";
        echo "<br>";
    }
?>
<form id="formTasks" name="formTasks" action="<?php echo $actionUrl?>" method="post">
    <!-- Main Tasks Table -->
    <table cellpadding="0" cellspacing="0" border="0" width="95%" class="contentTable" style="padding:2px; margin: 2px;" id="tableTasks">
        <?php
        $tasks = (array)$data->tasks;;
        drawTaskTable($tasks);
        ?>
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
    <table id="tableEdit" width="95%" cellpadding="0" cellspacing="0" border="0" class="contentTable">
    <tr>
        <td colspan="2">
            <span class="editTitle"><a name="editfield"><i><b><?php echo $editTitle ?></b></i></a></span>
            <br/>&nbsp;
        </td>
    <tr>
        <td valign="top" width="90">
            &nbsp;Title: &nbsp;
        </td>
        <td width="*">
            <input type="text" size="45" name="editTaskTitle" id="editTaskTitle" value="<?php echo $editTask->title ?>"/>
        </td>
    </tr>
    <tr>
        <td valign="top" width="90">
            &nbsp;Notes: &nbsp;
        </td>
        <td width="*">
            <textarea name="editTaskNotes" cols="40" rows="5" id="editTaskNotes"><?php echo $editTask->notes ?></textarea>
        </td>
    </tr>
    </table>
    <img src="images/spacer.gif" height="4">

    <!-- Control Buttons -->
    <table id="tableControls" width="95%" cellpadding="0" cellspacing="0" border="0" class="contentTable">
    <tr>
        <td align="left">
            <span id="divCancel"><a href="<?php echo $actionUrl ?>">Cancel Changes</a></span>
        </td>
        <td align="right">
            <input type="hidden" name="editTaskID" value="<?php echo $editGUID?>">
            <span id="divCleanup"><a href="<?php echo $actionUrl ?>&cleanup=complete">Clean-up</a></span>&nbsp;
            <span id="divSave"><input id="btnSubmit" type="submit" value="Save Changes" class="button"></span>
        </td>
    </tr>
    </table>
    <input type="hidden" name="dosubmit" value="on"/>

</form>

<audio preload="auto" style="display:none">
  <source src="sounds/completed1.mp3" type="audio/mp3">
  <source src="sounds/delete1.mp3" type="audio/mp3">
  <source src="sounds/flick1.mp3" type="audio/mp3">
  <source src="sounds/trash1.mp3" type="audio/mp3">
</audio> 
</body>
</html>
