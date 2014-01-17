#!/bin/bash
#This script is intended to install all the needed system packages to run website
#Only for apt now
apt-get install php5-common
apt-get install php5-cli
apt-get install php5-xdebug
apt-get install php5-pgsql
apt-get install php5-pear
apt-get install php5-curl
apt-get install postgresql
apt-get install postgresql-client
apt-get install postgresql-contrib
apt-get install postgresql-common
apt-get install nodejs
apt-get install node
curl https://npmjs.org/install.sh | sh
#do this local install in NodeJS server dir
#npm install socket.io
npm install -g node-gyp







#CentOS
#sudo yum groupinstall "Development tools"
#sudo yum install zlib-devel bzip2-devel openssl-devel ncurses-devel sqlite-devel readline-devel tk-devel
#sudo yum install php
#sudo yum install php-pecl-xdebug.x86_64
#sudo yum install php-pgsql.x86_64
#suddenly no python 3.3 on the system. See installation tutorial here:
#http://toomuchdata.com/2012/06/25/how-to-install-python-2-7-3-on-centos-6-2/
#sudo yum install postgresql postgresql-server
#sudo yum install postgresql-contrib.x86_64
#sudo yum install php-phpunit-PHPUnit.noarch
#sudo yum install php-phpunit-PHPUnit-SkeletonGenerator.noarch
