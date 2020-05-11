# FROM bowen31337/flarum:debian
# FROM bowen31337/flarum:alpine

FROM ubuntu:16.04
# install requirements
RUN apt update
RUN apt install -y nginx vim curl git software-properties-common unzip mariadb-client mariadb-server

# install mysql
# RUN apt -y install expect
# COPY mysql_install.sh /home/
# RUN chmod 774 /home/mysql_install.sh
# RUN /home/mysql_install.sh

# install php7.2 with extensions
RUN LC_ALL=C.UTF-8 add-apt-repository ppa:ondrej/php
RUN apt update
RUN apt install -y php7.2-fpm php7.2-common php7.2-mbstring php7.2-xmlrpc php7.2-soap php7.2-mysql php7.2-gd php7.2-xml php7.2-cli php7.2-zip php7.2-curl

#install composer
RUN curl -sS https://getcomposer.org/installer |  php -- --install-dir=/usr/local/bin --filename=composer

# install laravel
# WORKDIR /var/www/flarum
WORKDIR /var/www/laravel
COPY . .
# COPY app/. .
RUN chmod -R 777 .
RUN chown -R www-data:www-data .

# setup nginx for flarum
RUN rm /etc/nginx/sites-enabled/default
# COPY ./flarum.conf /etc/nginx/sites-available/flarum.conf
COPY ./laravel.conf /etc/nginx/sites-available/laravel.conf
# RUN ln -s /etc/nginx/sites-available/flarum.conf /etc/nginx/sites-enabled/
RUN ln -s /etc/nginx/sites-available/laravel.conf /etc/nginx/sites-enabled/

# copy some extra scripts
WORKDIR /home/
# COPY start-flarum.sh /home/
COPY start-laravel.sh /home/
RUN chmod 774 /home/*

EXPOSE 80

# CMD /home/start-flarum.sh && tail -f /dev/null
CMD /home/start-laravel.sh && tail -f /dev/null



# COPY --chown=www-data:www-data  app/. /home/site/wwwroot/
# RUN chmod -R 777 /home/site/wwwroot/storage/
# RUN mkdir -p /html
# COPY 50x.html /html/50x.html

# RUN apk update \
#   && apk add --no-cache apache2-utils

### COPY the working dir into docker image build

# COPY app/. /home/site/wwwroot/
# COPY  ./api.conf /etc/nginx/conf.d/api.conf

### comment following lines to disable base auth
# COPY  ./auth.conf /etc/nginx/conf.d/auth.conf
# RUN htpasswd -c -b /etc/nginx/auth.htpasswd dev dev

### docker-compose.yaml 使用方法
# 1.    新建文件夹,然后去https://dev.azure.com/hinterlands/seethrough/_git/flarum  把代码pull下来， 新建一个branch
# 2.     把docker-compose.yaml 复制到根目录去
# 3.    在根目录下运行docker build -t flarum_sit:debian .
# 4.    确保3000端口没有被占用，命令 docker-compose up -d
# 5.    去localhost:8776 用phpmyadmin 将数据库倒入到flarumdb， phpMyAdmin的用户名密码为root/12345678
# 6.    去setting 表修改maicol07-sso.post_logout_redirect_url  为 http://localhost:3000
# 去setting 表修改maicol07-sso.redirect_url 为 http://localhost:3000/auth/sso
# 7.    打开localhost:3000
###

# sudo docker run -d -p 80:80 nadi106/flarum:0.1.0-beta.8
# docker build -t laravel_shop:ubuntu .
# docker run --name laravel_shop -d -v /Users/steven/Projects/PhpProjects/Laravel/laravel-shop:/var/www/laravel -p 6000:80 laravel_shop:ubuntu