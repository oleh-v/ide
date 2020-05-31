#!/bin/bash

docker swarm init >/dev/null 2>&1 || true
docker network create -d overlay --attachable net_proxy || true
docker network create -d overlay --attachable net_data || true

mkdir projects || true
chown root:docker projects
chmod 775 projects

docker build -t mysql_ide ./config/docker/mysql
docker build -t php_ide ./config/docker/php --build-arg docker_gid=$(getent group docker | awk -F ":" '{print $3}')

cp env/ide.yml config/docker/ide.yml
sed -i "s/{{hostname}}/$HOSTNAME/g" config/docker/ide.yml
sed -i "s?{{envpwd}}?$PWD?g" config/docker/ide.yml

cp env/config.json env/www/config.json
sed -i "s?{{envpwd}}?$PWD?g" env/www/config.json

find $(pwd) -type f -name "*.sh" -exec chmod +x {} \;

docker stack deploy -c ./config/docker/ide.yml ide
