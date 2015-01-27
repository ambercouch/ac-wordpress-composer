#WordPress Submodule Wrapper

A simple wrapper to get wordpress working as a submodule

##How to use

1 Clone this repo.

  $ git clone https://github.com/ambercouch/wordpress-submodule-wrapper.git your-wp-folder 

2 Initiate submodules to download WordPress.

  $ git submodule update --init --recursive

3 Copy wp-config-sample.php and rename it for your environment (See Environment Config Files).

4 Add your database info and your unique [salt](https://api.wordpress.org/secret-key/1.1/salt/)
to your new config file.

5 Your done. browse to you local url and install wordpress as normal 

nb. By default your admin dashboard will be at /cms/wp-admin

##Environment Config Files

You can create, up to three environment config files with your database info for 
that environment, by renaming wp-config-sample.php. 

- production : production-config.php (default)
- development : development-config.php
- local : local-config.php

Which config file is used is decided by your url. By default production-config.php 
is used. If your url contains *.local* eg. www.my-website.local then local-config.php
is used. If your url contains *dev.* eg. dev.my-website.com then development-config.php 
is used.

##Why should I install WordPress like this?

**Because I am dogmatic**