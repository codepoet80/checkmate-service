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
    <link rel="icon" href="icon.png" type="image/png">
    <style>
        small { font-size: 13px; }
    </style>
    <script>
        function swapTech() {
            if (document.getElementById("divDisagree")) {
                document.getElementById("divDisagree").innerHTML = "<input class=\"button\" type=\"button\" id=\"btnDisagree\" value=\"Disagree\" onclick=\"document.location='login.php'\"/>"
            }
            if (document.getElementById("divLogin")) {
                document.getElementById("divLogin").innerHTML = "<input class=\"button\" type=\"button\" id=\"btnLogin\" value=\"Log-in\" onclick=\"document.location='login.php'\"/>"
            }
        }
    </script>
    <?php
    function getUserIP()
    {
        // Get real visitor IP behind CloudFlare network
        if (isset($_SERVER["HTTP_CF_CONNECTING_IP"])) {
                $_SERVER['REMOTE_ADDR'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
                $_SERVER['HTTP_CLIENT_IP'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
        }
        $client  = @$_SERVER['HTTP_CLIENT_IP'];
        $forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
        $remote  = $_SERVER['REMOTE_ADDR'];

        if(filter_var($client, FILTER_VALIDATE_IP))
        {
            $ip = $client;
        }
        elseif(filter_var($forward, FILTER_VALIDATE_IP))
        {
            $ip = $forward;
        }
        else
        {
            $ip = $remote;
        }

        return $ip;
    }

    if (isset($_POST["agreedBy"]))
    {
        $readURL = get_function_endpoint("new-user");
        $json = file_get_contents($readURL); 
        $data = json_decode($json);
    }
    ?>
</head>
<body background='images/chessboard.jpg' class="login" onload="swapTech()">

<table width="100%" height="100%" border="0" id="tableLayout">
    <tr>
        <td width="100%" height="100%" border="0" id="tdLayout" align="center">
            <form method="POST">
                <table width="600" height="300" border="1" class="tableBorder">
                    <tr>
                        <td>
                            <table width="100%" height="100%" bgcolor="lightgray" border="0" class="tableLogin" cellpadding="5">
                                <tr>
                                    <td colspan="4" align="center">
                                        <img src="images/icon3-64.png" style="margin-top:8px;"/><br/>
                                        <b>Check Mate - User Agreement</b><br/>
                                        &nbsp;
                                    </td>
                                </tr>
                                <tr>
                                    <td width="20">&nbsp;</td>
                                    <td colspan="2">
                                        Checkmate is free to use, and free to host. If you want to host it yourself, visit the <a href="https://github.com/codepoet80/checkmate-service">GitHub repo</a> for more information.<br>
                                        If you want to use this version, there are a few things you need to agree to...
                                        <small>
                                        <ul>
                                            <li>There is no guarantee of privacy or performance. This is a shared server, and because this service was designed for retro devices user content is not encrypted, and the only protection is through obfuscation -- no modern security is possible.</li>
                                            <li>The host of this service will comply with any legal requests for logs or data. Do not use this service for illegal activity or content, your IP address will likely be visible in the logs, and the host provides no protection or idemnification.</li>
                                            <li>The host of this service reserves the right to deny service to anyone whose use of the service is not in good faith or has the appearance of being unreasonable, damaging, or irresponsible.</li>
                                            <li>This service may go down without notice, and provides no SLA.</li>
                                        </ul>
                                        </small>
                                    </td>
                                    <td width="20">&nbsp;</td>
                                </tr>
                                <?php
                                if (isset($_POST["agreedBy"]) && isset($data) && isset($data->move) && isset($data->grandmaster)) {
                                //Never do PHP + HTML like this. Very spaghetti.
                                ?>
                                <tr>
                                    <td width="20">&nbsp;</td>
                                    <td colspan="2">
                                    Record this information; you will use it to log-in to your To Do list. It is not case sensitive. It cannot be changed, and you should not share it publicly.
                                    </td>
                                    <td width="20">&nbsp;</td>
                                </tr>
                                <tr>
                                    <td width="20">&nbsp;<input type="hidden" name="agreedBy" value="<?php echo getUserIP() ?>"/></td>
                                    <td align="left" valign="bottom"><b>Your Chess Move:</b><br> <?php echo $data->move ?></td>
                                    <td align="left" valign="bottom"><b>Your Grand Master:</b><br> <?php echo $data->grandmaster ?></td>
                                    <td width="20">&nbsp;</td>
                                </tr>
                                <tr>
                                    <td width="20">&nbsp;</td>
                                    <td colspan="2" align="center">
                                        <div id="divLogin"><a href="login.php">Log-In</a></div>
                                    </td>
                                    <td width="20">&nbsp;</td>
                                </tr>
                                <?php
                                }
                                else {
                                ?>
                                <tr>
                                    <td colspan="4" align="center">
                                    Click the appropriate button below to indicate your response to these terms.
                                    </td>
                                </tr>
                                <tr>
                                    <td width="20">&nbsp;<input type="hidden" name="agreedBy" value="<?php echo getUserIP() ?>"/></td>
                                    <td align="left" valign="bottom"><div id="divDisagree"><a href="login.php">Disagree</a></div></td>
                                    <td align="right" valign="bottom"><input class="button" type="submit" id="btnSubmit" value="Agree"/><img src="images/spacer.gif" width="20"></td>
                                    <td width="20">&nbsp;</td>
                                </tr>
                                <?php
                                }
                                ?>
                                <tr>
                                    <td colspan="4"></td>
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