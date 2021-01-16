<?php
    setcookie("grandmaster", "", time() - 3600);
    include("common.php");
    include("web-common.php");
?>

<html>
<head>
    <title>Check Mate - Your To Do List Anywhere</title>
    <link rel="shortcut icon" href="favicon.ico">
    <link rel="stylesheet" href="style.css">
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=1" />
    <script>
        function swapTech() {
            document.getElementById("divNewUser").innerHTML = "<input class='button' type='button' id='btnNew' value='New Game' onclick='createNew()'/>"
        }
        function createNew() {
            alert ("I'll create a new game for you!");
        }
        function checkSubmit() {
            //validate input
                return true;
        }
    </script>
</head>
<body background='images/chessboard.jpg' class="login" onload="swapTech()">
<?php
//Debugging
$errorMsg = "DEBUG...<br>";

?> 
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
                                        <img src="images/icon-64.png" /><br/>
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
                                    <td align="left" valign="bottom"><div id="divNewUser"><a href="new-user.php">New Game</a></div></td>
                                    <td align="right" valign="bottom"><input class="button" type="submit" id="btnSubmit" value="Login"/><img src="images/spacer.gif" width="20"></td>
                                </tr>
                                <tr>
                                    <td colspan="3" align="center"><small><a href="privacy.html">Privacy Info</a></small><br/>&nbsp;</td>
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