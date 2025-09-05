<!DOCTYPE html>
<html lang="en">
<?php
//This file is only used for advertising on a hosting webserver

//App Details
$description = "Check Mate is a cross platform to-do list app created and provided by webOS Archive for retro and modern devices.";
$title = "Check Mate";
$subtitle = " | Your To-Do List Anywhere!";
$github = "https://github.com/codepoet80/checkmate-service";
$pwaLink = "https://store.app/checkmate-wosa-link";
$githubLink = "https://github.com/codepoet80/enyo2-checkmate/releases";
$museumLink = "https://appcatalog.webosarchive.org/showMuseum.php?search=checkmate";
$icon = "assets/icon.png";

//Figure out what protocol the client wanted
if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
	$PROTOCOL = "https";
} else {
	$PROTOCOL = "http";
}
?>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">

  <meta name="description" content="<?php echo $description; ?>">
  <meta name="keywords" content="webos, firefoxos, pwa, rss">
  <meta name="author" content="webOS Archive">
  <meta property="og:title" content="<?php echo $title; ?>">
  <meta property="og:description" content="<?php echo $description; ?>">
  <meta property="og:image" content="https://<?php echo $_SERVER['SERVER_NAME'] ?>/hero.png">

  <meta name="twitter:card" content="app">
  <meta name="twitter:site" content="@webOSArchive">
  <meta name="twitter:title" content="<?php echo $title; ?>">
  <meta name="twitter:description" content="<?php echo $description; ?>">

  <title><?php echo $title . $subtitle; ?></title>
  
  <link id="favicon" rel="icon" type="image/png" sizes="64x64" href="<?php echo $icon;?>">
  <link href="<?php echo $PROTOCOL . "://www.webosarchive.org/app-template/"?>web.css" rel="stylesheet" type="text/css" >
</head>
<body>
<?php

$docRoot = "./";
echo file_get_contents("https://www.webosarchive.org/menu.php?docRoot=" . $docRoot . "&protocol=" . $PROTOCOL);
?>

  <table width="100%" border=0 style="width:100%;border:0px"><tr><td align="center" style="width:100%;height:100%;border:0px">
  <div id="row">
    <div id="content" align="left">
      <h1><img src="<?php echo $icon;?>" width="60" height="60" alt=""/><?php echo $title; ?></h1>
      <p><?php echo $description; ?></p>
      <p>Available for most platforms as a Progressive Web App, on Android, and the webOS App Museum for webOS devices.</p>
      <p>View the source and contribute on <?php echo "<a href='" . $github . "'>GitHub</a>"?>.</p>
      <p class="center">
        <?php if (isset($pwaLink)) { ?>
        <a class="download-link" href="<?php echo $pwaLink; ?>">
          <img src="<?php echo $PROTOCOL . "://www.webosarchive.org/app-template/"?>pwa-badge.png" width="200" height="59" alt="Install the PWA" />
        </a>
        <?php } ?>
        <?php if (isset($githubLink)) { ?>
        <a class="download-link" href="<?php echo $githubLink; ?>">
          <img src="<?php echo $PROTOCOL . "://www.webosarchive.org/app-template/"?>github-badge.png" width="200" height="59" alt="Get it on GitHub" />
        </a>
        <?php } ?>
        <?php if (isset($museumLink)) { ?>
        <a class="download-link" href="<?php echo $museumLink; ?>">
          <img src="<?php echo $PROTOCOL . "://www.webosarchive.org/app-template/"?>museum-badge.png" width="200" height="59" alt="Find it in the App Museum" />
        </a>
        <?php } ?>
      </p>
    </div>
    <div id="hero">
      <img src="hero.png" width="480" alt="<?php echo $title ?>" />
    </div>
  </div>
  <div id="footer">
    &copy; Othello Ventures and webOSArchive.
    <div id="footer-links">
      <a href="<?php echo $PROTOCOL . "://www.webosarchive.org/privacy.html"?>">Privacy Policy</a>
    </div>
  </div>
  </td></tr></table>
</body>
</html>
