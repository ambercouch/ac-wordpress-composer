#Ac WordPress Composer

Based on [Ambercouch/wordpress-submodule-wrapper](https://github.com/ambercouch/wordpress-submodule-wrapper)

Install WordPress using git and composer. Includes multiple environments

##How to use

1 Clone this repo.

     $ git clone https://github.com/ambercouch/ac-wordpess-composer.git your-site-folder

2 Install WordPress and dependencies.

     $ composer install

3 Copy ac-config-local-sample.php and rename it ac-config-local.php (See [Environment Config Files](#environment-config-files) for dev and production configs).

4 Add your database info and your unique [salt](https://api.wordpress.org/secret-key/1.1/salt/)
to your *new* config file.

5 Your done. Browse to you local url and install wordpress as normal. 

nb. By default your admin dashboard will be at /cms/wp-admin

##Environment Config Files

Wordpress will try to guess your environment. Just use the correct file that works for your domian

- production : ac-config-production-sample.php (Default eg Any domain/ip that is not local or development)
- development : ac-config-development-sample.php (Any domain that starts dev.)
- local : ac-config-local-sample.php (Any domain that ends .local )

Which config file is used is decided by your url. By default production-config.php 
is used. If your url contains *.local* eg. www.my-website.local then ac-config-local.php
is used. If your url contains *dev.* eg. dev.my-website.com then ac-config-development.php 
is used.

##Why should I install WordPress like this?

*Composer is better than git submodule*
*Because I am dogmatic*
