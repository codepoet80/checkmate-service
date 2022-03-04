# checkmate-service
A simple, retro-device friendly Task List utility with a chess theme

Don't @ me cause I'm using tables for layout. This web app works and looks great on OmniWeb 1.0 from 1996 through to the latest Google Chrome -- your fancy CSS can't do that.

## System Requirements
requires php-curl
web server user will need read/write access to the notations folder and all contents

## Remote Clients
This service supports a number of remote client apps. If you intend to support web-based apps, ensure you have CORS configured properly on your web server. Here is the CORS configuration for my Apache server:

```

```

## Modern Client
You can add a modern client by cloning the [enyo2-checkmate repo](https://www.github.com/codepoet80/enyo2-checkmate) and linking it to a subfolder/virtual directory called `/app`