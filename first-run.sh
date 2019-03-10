#!/bin/bash

docker swarm init >/dev/null 2>&1
docker network create -d overlay --attachable traefik_net >/dev/null 2>&1
docker network create -d overlay --attachable mysql_net >/dev/null 2>&1

if [ "`grep locked /proc/$(ps --no-headers -o pid -C dockerd | tr -d ' ')/limits | awk '{print $4" "$5}'`" != "unlimited unlimited" ]; then
    echo -e "[Service]\nLimitMEMLOCK=infinity\nLimitNOFILE=unlimited" | SYSTEMD_EDITOR=tee systemctl edit docker.service
    systemctl daemon-reload
    systemctl restart docker
fi

if (( "`sysctl vm.max_map_count | awk '{print $3}'`" < "262144" ))
then
    echo 'vm.max_map_count=262144' >> /etc/sysctl.conf
    sysctl -w vm.max_map_count=262144
fi

chown root:root ./config/docker/filebeat/filebeat.yml
chmod 644 ./config/docker/filebeat/filebeat.yml
chmod -R 775 ./data/elk/elastic/es_data

docker build -t mysql_ide ./config/docker/mysql
docker build -t php_ide ./config/docker/php --build-arg docker_gid=$(getent group docker | awk -F ":" '{print $3}')

cp ide.yml config/docker/ide.yml
sed -i "s/{{hostname}}/$HOSTNAME/g" config/docker/ide.yml

docker stack deploy -c ./config/docker/ide.yml ide
