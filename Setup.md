# Introduction #

The first version does not have a setup wizard.
You have to install the media library manually.

  1. get a clone from git
  1. prepare your webserver
  1. customize configuration

# Get a Clone from Git #

To obtain a copy from the source code see: http://code.google.com/p/personal-mediabox/source/checkout

# Prepare your webserver #

This setup guide assumes that you use an apache 2.x webserver.
It should be possible to run the personal-mediabox on other webservers, but we haven't tried yet.
We recommend to host the personal-mediabox on a virtual host.

  1. configure name resolution
  1. setup the virtual host
  1. add h.264 handler

## Configure Name Resolution ##

You have a DNS server available
Add your server to your DNS (see http://www.google.de/search?q=DNS%20a-record)

You don't have a DNS server available
Use local name resolution (see http://www.google.de/search?q=hosts%20files)

## Setup the Virtual Host ##

| to be described |
|:----------------|

## Add h.264 handler ##

| to be described |
|:----------------|

# Customize Configuration #

The following configuration files must be adapted to your environment to run the personal-mediabox
  * %BASE\_PATH%/application/config/imdbapi.php
  * %BASE\_PATH%/application/config/mediainfo.php
  * %BASE\_PATH%/application/config/videogallery.php