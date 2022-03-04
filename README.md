# checkmate-service
A simple, retro-device friendly Task List utility with a chess theme

Don't @ me cause the built-in client uses tables for layout -- it works and looks great on OmniWeb 1.0 from 1996 through to the latest Google Chrome -- your fancy CSS can't do that. If you prefer a more modern client, check out [enyo2-checkmate](https://www.github.com/codepoet80/enyo2-checkmate)

## System Requirements
requires `php-curl`

The web server user will need read/write access to the notations folder and all contents

## Remote Clients
This service supports a number of remote client apps. If you intend to support web-based apps, ensure you have CORS configured properly on your web server. Here is the CORS configuration for my Apache server:

```
<VirtualHost *:80>
	ServerName checkmate.webosarchive.com
	DocumentRoot /var/www/checkmate
	<Directory /var/www/checkmate>
		Header set Access-Control-Allow-Origin "*"
		Header set Access-Control-Allow-Methods GET,POST,PUT,DELETE,OPTIONS
	</Directory>
</VirtualHost>
```

## Modern Client
You can add a modern client by cloning the [enyo2-checkmate repo](https://www.github.com/codepoet80/enyo2-checkmate) and linking it to a subfolder/virtual directory called `/app`