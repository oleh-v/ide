# ELK
#
# 0.
# docker swarm init
# docker network create -d overlay --attachable traefik_net
# docker network create -d overlay --attachable mysql_net
#
#
# 1.
echo -e "[Service]\nLimitMEMLOCK=infinity" | SYSTEMD_EDITOR=tee systemctl edit docker.service
systemctl daemon-reload
systemctl restart docker
# Check:
# grep locked /proc/$(ps --no-headers -o pid -C dockerd | tr -d ' ')/limits
#
# 2.
# persistent:
# grep vm.max_map_count /etc/sysctl.conf
# vm.max_map_count=262144
#
# apply instantly:
# sysctl -w vm.max_map_count=262144
# 
# https://gist.github.com/wshayes/cf943d483b933aeece2f7ba80c42a97a
# 
#
# 3. 
# chown root:root /host/config/docker/filebeat/filebeat.yml
# chmod 644 /host/config/docker/filebeat/filebeat.yml
#
# 4. 
# ???
# chmod -R 777 /host/data/elk/elastic/es_data
#
#



