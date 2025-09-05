<?php
//This file is only used for advertising on a hosting webserver

//Figure out what protocol the client wanted
if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
	$PROTOCOL = "https";
} else {
	$PROTOCOL = "http";
}
$docRoot = "./";
$appTitle = "Check Mate";
echo file_get_contents("https://www.webosarchive.org/app-template/header.php?docRoot=" . $docRoot . "&appTitle=" . $appTitle . "&protocol=" . $PROTOCOL);
?>
    <style>
        body { background-color: white;}
    </style>
    <div style="font-family:arial,helvetica,sans-serif;margin:15px;" align="center">
    <p>Check Mate is a cross platform to-do list app created by provided by <a href="http://www.webosarchive.org">webOS Archive</a> for retro and modern devices.<br/>
    Choose the experience that's best for your platform...</p>
    <table style="margin-left:15%;margin-right:20%;font-size:small;">
        <tr><td width="22%" align="right"><b><a href="retro.php">Retro</a></b></td><td style="padding-left:18px">Best for pre-HTML5 browsers, as far back as OmniWeb, Netscape and Internet Explorer!</td></tr>
        <tr><td width="22%" align="right"><b><a href="/app" target="_blank">PWA</a></b></td><td style="padding-left:18px">Progressive Web Apps work on modern browsers, and can be pinned to your home screen or dock on modern platforms.</td>
        <tr><td width="22%" align="right"><b><a href="https://play.google.com/store/apps/details?id=com.webosarchive.checkmatehd">Android</a></b></td><td style="padding-left:18px">The PWA bundled for distribution on Google Play.</td></tr>
        <tr><td width="22%" align="right"><b><a href="https://appcatalog.webosarchive.org/showMuseum.php?search=check+mate">webOS</a></b></td><td style="padding-left:18px">Versions built for legacy (mobile) webOS and modern LuneOS.</td></tr>
    </table>
    <p>Check Mate is open source! Code and Releases can be found here:
    <table style="margin-left:20%;margin-right:20%;font-size:small;">
        <tr><td align="center"><a href="https://github.com/codepoet80/checkmate-service">Back-end code (including the retro web interface)</a></td></tr>
        <tr><td align="center"><a href="https://github.com/codepoet80/enyo2-checkmate">PWA code, including Android, webOS and LuneOS</a></td></tr>
        <tr><td align="center"><a href="https://github.com/codepoet80/webos-checkmate">Mojo version for webOS only</a></td></tr>
    </div>
</body>
</html>