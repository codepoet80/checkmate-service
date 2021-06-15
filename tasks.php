<?php
    include("common.php");
    include("web-functions.php");
    include("tasks-functions.php");
?>
<html>
<head>
<?php
    include("web-meta.php");
?>
<script type="text/javascript" src="checkmate-ajax.js"></script> 
<script>
    var actionUrl = "<?php echo $actionUrl ?>";
    checkmate.actionUrl = actionUrl;
    var usegm = "<?php echo base64_encode ($grandmaster) ?>";
    var usenotation = "<?php echo $data->notation ?>";
    var taskdata = JSON.parse("<?php echo addslashes(json_encode($data->tasks)); ?>");
    var xhr = checkmate.detectXHR();
    //alert ("xhr is: " + xhr);
    //xhr = false;

    function swapTech()
    {
        document.getElementById("imgIcon").src = "images/icon.png";
        document.getElementById("tableControls").style.marginTop = "14px";
        document.getElementById("divLogout").innerHTML = "<input type=\"button\" value=\"Log Out\" class=\"button\" onclick=\"document.location='index.php'\"/>";
        document.getElementById("divCancel").innerHTML = "<img src=\"images/refresh.png\" class=\"controlButton\" onclick=\"document.location='<?php echo $actionUrl?>'\"/>";
        document.getElementById("divCleanup").innerHTML = "<img src=\"images/sweep.png\" class=\"controlButton\" onclick=\"doCleanup()\"/>";
        document.getElementById("btnSubmit").style.display = "none";
        document.getElementById("divSave").insertAdjacentHTML("beforeend", "<img src=\"images/save.png\" class=\"controlButton\" onclick=\"doSave()\"/>");
        //TODO: Detect sufficient CSS and remove edit box, in favor of some pop-up UI
            //TODO: Invent pop-up UI
    }

    function checkTask(checkbox){
        if (checkbox.checked) {
            var audio = new Audio('sounds/completed1.mp3');
        } else {
            var audio = new Audio('sounds/flick1.mp3');
        }
        audio.play();

        //TODO: Detect AJAX and change
        if (xhr) {
            console.log("Updating task " + checkbox.id);
            for (var i=0;i<taskdata.length; i++) {
                if (taskdata[i].guid == checkbox.id) {
                    updatetask = taskdata[i];
                }
            }
            if (updatetask) {
                updatetask.completed = checkbox.checked;
                checkmate.updateTask(usegm, usenotation, updatetask, function(response) {
                    if (!response) {
                        alert ("Error: No response from server!");
                    } else {
                        if (JSON.parse(response)) {
                            response = JSON.parse(response);
                            if (response.error) {
                                alert (response.error)
                            } else {
                                taskdata = response;
                            }
                        } else {
                            alert ("Error: Could not parse server response!");
                        }
                    }
                });
            } else {
                alert ("Error: Could not find task data to update!");
            }
        } else {
            setTimeout(() => {
                document.getElementById('formTasks').submit();
            }, 1000);
        }
    }

    function doTaskEdit(taskId) {
        //TODO: Detect AJAX and change
        var newURL = actionUrl + "&edit=" + taskId + "#editfield";
        document.location = newURL;
    }

    function doTaskDelete(taskId) {
        if (window.confirm("Are you sure you want to delete this task?")) {
                var audio = new Audio('sounds/delete1.mp3');
                audio.play();

                //TODO: Detect AJAX and change
                setTimeout(() => {
                        var newURL = actionUrl + "&delete=" + taskId + "#editfield";
                    document.location = newURL;
                }, 1000);

                //Works with AJAX
                var taskRow = document.getElementById("taskRow" + taskId);
                taskRow.parentNode.removeChild(taskRow);
        }
    }

    function doCleanup() {
        var audio = new Audio('sounds/trash1.mp3');
        audio.play();

        //TODO: Detect AJAX and change
        setTimeout(() => {
            var newURL = actionUrl + "&cleanup=complete";
        document.location = newURL;
        }, 1000);
    }

    function doSave() {
        document.getElementById('formTasks').submit();
        //TODO: Detect AJAX and change
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
    <table cellpadding="2" cellspacing="2" border="0" width="80%" class="contentTable">
        <tr><td colspan="3" id="taskTableFrameTop"><hr/></td></tr>
        <?php
        $tasks = (array)$data->tasks;
        foreach ($tasks as $task)
        {
            echo "\t\t<tr id=\"taskRow" . $task->guid . "\">\r\n";
            echo "\t\t\t<td><input type='checkbox' id='" . $task->guid . "' name='check[" . $task->guid . "]'";
            if ($task->completed)
                echo " checked";
            echo " onchange='checkTask(this)'/></td>\r\n";
            echo "\t\t\t<td valign='middle' width='100%' class='taskListDetailCell'><b>" . $task->title . "</b>";
            if ($task->notes != "") {
                echo "&nbsp; <img src='images/note.png' title='" . htmlentities($task->notes) . "' alt='" . htmlentities($task->notes) . "'/>";
            } 
            echo "</td>\r\n";
            echo "\t\t\t<td style='min-width: 60px;'>\r\n";
            echo "\t\t\t\t<span class=\"editLink\">  <a href=\"" . $actionUrl . "&edit=" . $task->guid . "#editfield\">Edit</a></span>\r\n";
            echo "\t\t\t\t<span class=\"editImageWrapper\"><img src=\"images/pencil.gif\" class=\"editImage\" onclick=\"doTaskEdit('" . $task->guid . "')\"></span>\r\n";
            echo "\t\t\t\t<span class=\"deleteLink\"><a href=\"" . $actionUrl . "&delete=" . $task->guid . "\">Delete</a></span>\r\n";
            echo "\t\t\t\t<span class=\"deleteImageWrapper\"><img src=\"images/delete.gif\" class=\"deleteImage\" onclick=\"doTaskDelete('" . $task->guid . "')\"></span>\r\n";
            echo "\t\t\t</td>\r\n\t\t</tr>\r\n";
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
            <span id="divSave"><input id="btnSubmit" type="submit" value="Save Changes" class="button"></span>
        </td>
    </tr>
    </table>
    <input type="hidden" name="dosubmit" value="on"/>
    
</form>
</body>
</html>
