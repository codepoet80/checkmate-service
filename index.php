<?php
    setcookie("grandmaster", "", time() - 3600);
    include("common.php");
?>

<html>
<head>
    <title>Check Mate - Your To Do List Anywhere</title>
    <link rel="shortcut icon" href="favicon.ico">
    <link rel="stylesheet" href="style.css">
    <link rel="icon" href="images/icon.png" type="image/png">
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=1" />
    <meta http-equiv="pragma" content="no-cache">
    <script>
        function swapTech() {
            document.getElementById("divNewUser").innerHTML = "<input class=\"button\" type=\"button\" id=\"btnNew\" value=\"New Game\" onclick=\"document.location='agreement.php'\"/>";
            document.getElementById("imgIcon").src = "images/icon.png";
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