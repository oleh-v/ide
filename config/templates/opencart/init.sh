#!/bin/bash
set -e

if [ ! -f /var/www/html/index.php ]; then
        # init if empty
#        wget https://github.com/opencart/opencart/releases/download/3.0.3.2/opencart-3.0.3.2.zip -O /tmp/opencart.zip
#        wget https://github.com/opencart/opencart/archive/2.1.0.2.zip -O /tmp/opencart.zip
        wget {{oc_url}} -O /tmp/opencart.zip
#        unzip /tmp/opencart.zip '*upload*' -d /tmp
        unzip /tmp/opencart.zip -d /tmp
        ls -l /tmp/
#        unzip /tmp/opencart.zip -d /tmp/opencart
        shopt -s dotglob nullglob
        mv {{upload_path}} /var/www/html/
#        mv /tmp/upload/* /var/www/html/
#        mv /tmp/opencart-xxxx/upload/* /var/www/html/
        mv config-dist.php config.php
        mv admin/config-dist.php admin/config.php
        mv .htaccess.txt .htaccess
        chown -R www-data:www-data /var/www
else
    echo "\n* Opencart Core already installed...";
fi

exec apache2-foreground