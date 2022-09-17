<?php
    setcookie("grandmaster", "", time() - 3600);
    include("common.php");

    if (file_exists("app/index.html"))
        echo "<script>var modernURL='app'</script>";
    if (is_found("app"))
        echo "<script>var modernURL='app'</script>";

function is_found($page)
{
    if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on')
         $url = "https://";
    else
         $url = "http://";
    $url.= $_SERVER['HTTP_HOST'];
    $url.= $_SERVER['REQUEST_URI'];
    $url.=$page;

    $options['http'] = array(
        'method' => "HEAD",
        'ignore_errors' => 1,
        'max_redirects' => 0
    );
    $body = file_get_contents($url, NULL, stream_context_create($options));
    sscanf($http_response_header[0], 'HTTP/%*d.%*d %d', $code);
    return $code === 200 || $code === 301;
}
?>

<html>
<head>
    <title>Check Mate - Your To Do List Anywhere</title>
    <link rel="shortcut icon" href="favicon.ico">
    <link rel="icon" href="images/icon.png" type="image/png">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="notifications/notifications.css">
    <script src="notifications/notifications.js"></script>
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=1" />
    <meta http-equiv="pragma" content="no-cache">
    <script>
        function swapTech() {
            document.getElementById("divNewUser").innerHTML = "<input class=\"button\" type=\"button\" id=\"btnNew\" value=\"New Game\" onclick=\"document.location='agreement.php'\"/>";
            document.getElementById("imgIcon").src = "images/icon.png";

            if (modernURL=="app") {
                try {
                    var prefix = (Array.prototype.slice
                    .call(window.getComputedStyle(document.documentElement, ""))
                    .join("") 
                    .match(/-(moz|webkit|ms)-/))[1];
                    console.log("Found browser prefix: " + prefix);
                    if (["moz","webkit"].indexOf(prefix) != -1) {
                        modernURL = window.location.href.split("?")[0].replace("index.php", "");
                        modernURL = (modernURL + "/app").replace("//app", "/app").replace("http://", "https://");
                        var myNotification = window.createNotification({});
                        myNotification({ 
                            title: 'Hello Modern Browser!',
                            displayCloseButton: true,
                            theme: 'info',
                            message: 'You\'re viewing the retro-friendly landing. Did you know there\'s a modern web app you can use, after you sign up?\r\n\nJust go to ' + modernURL 
                        });
                    }
                } catch (e) {
                    //oh well
                }
            }
        }
        function checkSubmit() {
            if (document.getElementById("txtMove").value == "") {
                document.getElementById("txtMove").focus();
                return false;
            }
            if (document.getElementById("txtGrandmaster").value == "") {
                document.getElementById("txtGrandmaster").focus();
                return false;
            }
            return true;
        }
    </script>
</head>
<body background='images/chessboard.jpg' class="login" onload="swapTech()">
<table width="100%" height="100%" border="0" id="tableLayout">
    <tr>
        <td width="100%" height="100%" border="0" id="tdLayout" align="center">
            <form method="POST" action="tasks.php" onsubmit="return checkSubmit()">
                <table width="400" height="300" border="1" class="tableBorder">
                    <tr>
                        <td>
                            <table width="100%" height="100%" bgcolor="lightgray" border="0" class="tableLogin">
                                <tr>
                                    <td colspan="3" align="center">
                                        <img src="images/icon.gif" style="margin-top:8px;" id="imgIcon"/><br/>
                                        <b>Check Mate - Login</b><br/>
                                        &nbsp;
                                    </td>
                                </tr>
                                <tr>
                                    <td width="20">&nbsp;</td>
                                    <td width="150" align="right">Chess Move:&nbsp; </td>
                                    <td width="260"><input type="text" id="txtMove" name="move" size="25" /></td>
                                </tr>
                                <tr>
                                    <td width="20">&nbsp;</td>
                                    <td width="150" align="right">Grand Master: </td>
                                    <td width="260"><input type="text" id="txtGrandmaster" name="grandmaster" size="25" /></td>
                                </tr>
                                <tr>
                                    <td width="20">&nbsp;</td>
                                    <td colspan="2" align="left">&nbsp;<br/><input type="checkbox" id="chkUseGet" name="useGet" /> Use GET (Less secure, but Bookmark friendly)<br/>&nbsp;</td>
                                </tr>
                                <tr>
                                    <td width="20">&nbsp;</td>
                                    <td align="left" valign="bottom"><div id="divNewUser"><a href="agreement.php">New Game</a></div></td>
                                    <td align="right" valign="bottom"><input class="button" type="submit" id="btnSubmit" value="Login"/><img src="images/spacer.gif" width="20"></td>
                                </tr>
                                <tr>
                                    <td colspan="3" align="center"><small>Copyright 2021, Jonathan Wise.<br/><a href="https://github.com/codepoet80/checkmate-service">License and Open Source Info</a></small><br/>&nbsp;</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </form>
        </td>
    </tr>
</table>
</body>
</html> 
