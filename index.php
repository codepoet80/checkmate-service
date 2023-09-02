<html>
<head>
    <title>Check Mate - Your To Do List Anywhere</title>
    <link rel="shortcut icon" href="favicon.ico">
    <link rel="icon" href="images/icon.png" type="image/png">
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=1" />
    <meta http-equiv="pragma" content="no-cache">
</head>
<body>
<?php
    //Figure out what protocol the client wanted
    if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
      echo file_get_contents("https://www.webosarchive.org/menu.php?content=maps");
      $REQUEST_PROTOCOL = "https";
    }
    else {
      echo file_get_contents("http://www.webosarchive.org/menu.php?content=maps");
      $REQUEST_PROTOCOL = "http";
    }
?>
    <div style="font-family:arial,helvetica,sans-serif;margin:15px;">
    <table><tr><td><img src="images/icon.gif"></td><td><h1>Check Mate</h1></tr></table>
    <p>Check Mate is a retro-friendly, cross platform app, provided by <a href="http://www.webosarchive.org">webOS Archive</a>. Choose the experience that's best for your platform...</p>
    <ul>
        <li><b><a href="retro.php">Retro</a>:</b> best for pre-HTML5 browsers, as far back as OmniWeb, Netscape and Internet Explorer!</li>
        <li><b><a href="/app">PWA</a>:</b> Progressive Web Apps work on modern browsers, and can be pinned to your home screen, dock or Start Menu on modern platforms.</li>
        <li><b><a href="https://play.google.com/store/apps/details?id=com.webosarchive.checkmatehd">Android</a>:</b> The PWA, bundled for distribution on Google Play.</li>
        <li><b><a href="https://appcatalog.webosarchive.org/showMuseum.php?search=check+mate">webOS/LuneOS</a>:</b> Versions built for legacy (mobile) webOS and modern LuneOS.</li>
    </ul>
    <p>Check Mate is open source! Code and Releases can be found here:
    <ul>
        <li><b><a href="https://github.com/codepoet80/checkmate-service">Back-end code (including the retro web interface)</a></li>
        <li><b><a href="https://github.com/codepoet80/enyo2-checkmate">PWA code, including Android, webOS and LuneOS</a></li>
        <li><b><a href="https://github.com/codepoet80/webos-checkmate">Mojo version for webOS only</a></li>
    </ul>
    </div>
</body>
</html>