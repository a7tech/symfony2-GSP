#!/bin/sh

 #Mysql - Openssh
DEBIAN_FRONTEND=noninteractive apt-get install -y -q mysql-server openssh-server

# Make sshd run dir
mkdir /var/run/sshd
echo 'root:root' | chpasswd

apt-get update
apt-get upgrade -y

# PHP
apt-get install -y python-software-properties software-properties-common
add-apt-repository ppa:ondrej/php5-oldstable
 
apt-get update

apt-get install -y --force-yes php5-cli php5-mysql php5-curl php5-sqlite php5-intl libicu48 php5-gd 
apt-get install -y git
apt-get install -y curl
apt-get install -y supervisor

# Set timezone
perl -pi -e "s#;date.timezone =#date.timezone = Europe/Amsterdam#g" /etc/php5/cli/php.ini

# Make supervisor log dirs
mkdir -p /var/log/supervisor

# Java 7
echo "deb http://ppa.launchpad.net/webupd8team/java/ubuntu precise main" | tee -a /etc/apt/sources.list
apt-key adv --keyserver hkp://keyserver.ubuntu.com:80 --recv-keys EEA14886
apt-get update
echo oracle-java7-installer shared/accepted-oracle-license-v1-1 select true | /usr/bin/debconf-set-selections
apt-get install oracle-java7-installer -y

cd /
wget https://download.elasticsearch.org/elasticsearch/elasticsearch/elasticsearch-0.90.5.deb
dpkg -i elasticsearch-0.90.5.deb
rm elasticsearch-0.90.5.deb

mkdir -p /var/data/elasticsearch
chown -R elasticsearch:elasticsearch /var/data/elasticsearch

sh /usr/share/elasticsearch/bin/plugin --install lukas-vlcek/bigdesk
sh /usr/share/elasticsearch/bin/plugin --install mobz/elasticsearch-head
sh /usr/share/elasticsearch/bin/plugin --install elasticsearch/elasticsearch-lang-python/1.2.0