<?php

$postjson = file_get_contents('php://input'); 
if (isset($postjson)) {
    $postdata = json_decode($postjson);
    if (is_array($postdata))
        drawTaskTable($postdata);
}

function drawTaskTable($tasks) {
    echo "<tr><td colspan=\"4\" id=\"taskTableFrameTop\"><hr/></td></tr>";
    $currUrl = currentUrl();
    if (isset($tasks)) {
        foreach ($tasks as $task)
        {
            echo "\r\n";
            echo "\t\t<tr class=\"taskrow\" id=\"taskRow" . $task->guid . "\" ondragover=\"allowDrop(event)\" ondrop=\"drop(event)\">\r\n";
            echo "\t\t\t<td class=\"dragContainer\" width=\"30\">\r\n";
            echo "\t\t\t\t<img class=\"dragHandle\" src=\"images/spacer.gif\" id=\"drag$task->guid\" ondragenter=\"dragEnter(event)\" ondragleave=\"dragLeave(event)\" ondragstart=\"dragStart(event)\" draggable=\"true\">\r\n";
            echo "\t\t\t</td>\r\n";
            echo "\t\t\t<td class=\"taskChecker\" style=\"width:30px\" width=\"30\">\r\n";
            echo "\t\t\t\t<input type='checkbox' id='" . $task->guid . "' name='check[" . $task->guid . "]'";
            if ($task->completed)
                echo " checked";
            echo " onchange=\"taskModel.doCheckTask(this)\"/>\r\n";
            echo "\t\t\t<td valign=\"middle\" width=\"100%\">\r\n";
            echo "\t\t\t\t<span class=\"taskListDetailCell\"><b>" . $task->title . "</b>";
            if ($task->notes != "") {
                echo "&nbsp; <img src=\"images/note.gif\" title=\"" . htmlentities($task->notes) . "\" alt=\"" . htmlentities($task->notes) . "\"/>";
            } 
            echo "</span>\r\n";
            echo "\t\t\t</td>\r\n";
            echo "\t\t\t<td class=\"taskButtons\" style=\"min-width: 75px;\">\r\n";
            echo "\t\t\t\t<span class=\"editLink\"><a href=\"$currUrl&edit=$task->guid#editfield\">Edit</a></span>\r\n";
            echo "\t\t\t\t<span class=\"editImageWrapper\"><img src=\"images/spacer.gif\" class=\"editImage\" onclick=\"taskModel.doTaskEdit('$task->guid')\"></span>\r\n";
            echo "\t\t\t\t<span class=\"deleteLink\"><a href=\"$currUrl&delete=$task->guid\">Delete</a></span>\r\n";
            echo "\t\t\t\t<span class=\"deleteImageWrapper\"><img src=\"images/spacer.gif\" class=\"deleteImage\" onclick=\"taskModel.doTaskDelete('$task->guid')\"></span>\r\n";
            echo "\t\t\t</td>\r\n\t\t</tr>\r\n";
            echo "\t\t<tr><td colspan=\"3\"><img src=\"images/spacer.gif\" height=\"4\"/></div></td></tr>\r\n";
        }
    }
    echo "<tr><td colspan=\"4\" id=\"taskTableFrameBottom\"><hr></td></tr>";
}

function currentUrl() {
    $pageURL = (isset( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] == 'on') ? "//" : "//";
    $pageURL .= $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
    $url = explode( '?', $pageURL );
    return $url[0];
}
?>