# Raau01 project

## Deploy guideline

### 1. System requirement
OS: Ubuntu 20.04
CPU: >= 2 vCPU
RAM: >= 4G
Storage: >= 30Gb

SSH to server:

```sh
cd PATH_OF_PEM_FILE
sudo chmod -R 400 PEM_FILE.pem ubuntu@IP_SERVER
```
Run commands below:
```sh
sudo apt-get update
sudo apt-get install zip unzip
timedatectl
timedatectl list-timezones | grep -i asia
sudo timedatectl set-timezone Asia/Tokyo
date
```

### 2. Nginx
Run commands below:
```sh
sudo apt-get install nginx
cd /var/log
sudo mkdir raau01
```
Edit config file:
Run commands below:
```sh
cd /etc/nginx/sites-available/
sudo mv default default.bak
touch default
sudo vi default
```
Add content below:
>server {
>    listen   80;
>    listen   [::]:80;
>    server_name raau01.deha.vn;
>    root   /home/ubuntu/raau01-backend/public;
>    index  index.php index.html index.htm;

>    client_max_body_size 200M;

>    access_log /var/log/raau01/access.log;
>    error_log /var/log/raau01/error.log;

>    gzip on;
>    gzip_disable "msie6";
>    gzip_vary on;
>    gzip_proxied any;
>    gzip_comp_level 6;
>    gzip_buffers 16 8k;
>    gzip_http_version 1.1;
>    gzip_types text/plain text/css application/json application/x-javascript text/xml application/xml application/xml+rss text/javascript;

>    location / {
>        #auth_basic           "Restricted Access!";
>        #auth_basic_user_file /etc/nginx/conf.d/.htpasswd;
>        try_files $uri $uri/ /index.php?$args;
>    }

>    location ~ \.php$ {
>        try_files $uri =404;
>        include fastcgi_params;
>        fastcgi_pass 127.0.0.1:9000;
>        fastcgi_index index.php;
>        fastcgi_intercept_errors on;
>        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
>    }
>}


Run this commands after install: Nginx, PHP, source code:
```sh
sudo apt-get install snapd
sudo snap install --classic certbot
sudo service nginx stop
sudo certbot --nginx
sudo fuser -k 80/tcp
sudo fuser -k 443/tcp
sudo service nginx start
```

### 3. Mysql
Run commands below:
```sh
cd ~
wget -c https://dev.mysql.com/get/mysql-apt-config_0.8.11-1_all.deb
sudo dpkg -i mysql-apt-config_0.8.11-1_all.deb
sudo apt-get update
sudo apt-get install mysql-server
sudo mysql_secure_installation
sudo vi /etc/mysql/mysql.conf.d/mysqld.cnf
```
Commnet this line:
> #bind-address           = 127.0.0.1

```sh
sudo service mysql restart
mysql -uroot -p
```

Add new user and create database:
```sh
create database raau01;
CREATE USER 'raau01'@'localhost' IDENTIFIED BY 'YOUR PASSWORD';
GRANT ALL PRIVILEGES ON raau01.* TO 'raau01'@'localhost';
FLUSH PRIVILEGES;

CREATE USER 'raau01'@'%' IDENTIFIED BY 'YOUR PASSWORD';
GRANT ALL PRIVILEGES ON raau01.* TO 'raau01'@'%';
FLUSH PRIVILEGES;
```


### 4. PHP
Run commands below:
```sh
sudo apt update
sudo apt -y install software-properties-common
sudo add-apt-repository ppa:ondrej/php
sudo apt-get update
sudo apt -y install php8.0
php -v
sudo apt-get install php8.0-cli php8.0-common php8.0-json php8.0-opcache php8.0-mysql php8.0-mbstring php8.0-zip php8.0-fpm php8.0-xml php8.0-curl php8.0-pdo php8.0-sqlite
sudo apt-get install mcrypt php8.0-mcrypt
sudo vi /etc/php/8.0/fpm/php.ini
```
Change PHP config as below:
>upload_max_filesize = 200M
>max_file_uploads = 200
>max_execution_time = 1800
>max_input_time = 1800
>memory_limit = -1

Run commands below:
```sh
sudo vi /etc/php/8.0/fpm/pool.d/www.conf
```

Comment and add 2 lines as below:
>;listen = /run/php/php8.0-fpm.sock
>listen = 127.0.0.1:9000

Restart PHP-FPM:
```sh
sudo service php8.0-fpm restart
```

### 5. Source code
Run commands below:
Add ssh key:
```sh
ssh-keygen -t rsa -C "YOUR EMAIL"
vi ~/.ssh/id_rsa.pub
```
Add content to Gitlab -> profile -> SSH key

Run commands below:
```sh
cd ~
git clone https://gitlab.dehasoft.vn/dehasoft/raau01-backend.git
cd raau01-backend/
cp .env.example .env
```
Edit content as below:
>APP_DEBUG=false
>APP_URL=https://YOUR_DOMAIN

>DB_USERNAME=raau01
>DB_PASSWORD=YOUR_MYSQL_PASSWORD

>MAIL_MAILER=smtp
>MAIL_HOST=YOUR_MAIL_SERVER_INFO
>MAIL_PORT=YOUR_MAIL_SERVER_INFO
>MAIL_USERNAME=YOUR_MAIL_SERVER_INFO
>MAIL_PASSWORD=YOUR_MAIL_SERVER_INFO
>MAIL_ENCRYPTION=YOUR_MAIL_SERVER_INFO
>MAIL_FROM_ADDRESS=YOUR_MAIL_SERVER_INFO

Run commands below:
```sh
sudo chmod -R 777 storage bootstrap/cache
sudo apt-get install composer
composer install
git config core.fileMode false

wget https://phar.phpunit.de/phpunit-9.3.phar
sudo chmod +x phpunit-9.3.phar
sudo mv phpunit-9.3.phar /usr/bin/phpunit
phpunit --version
```

Set basic authenticate:
```sh
sudo apt install apache2-utils
sudo htpasswd -c /etc/nginx/conf.d/.htpasswd admin
```
Authenticate info:
>admin
>YOUR_AUTHENTICATE_PASSWORD

Enable basic authenticate:
```sh
sudo vi /etc/nginx/sites-available/default
```
Edit content as below:
> location / {
>    auth_basic           "Restricted Access!";
>    auth_basic_user_file /etc/nginx/conf.d/.htpasswd;
>}

Run commands below:
```sh
cd ~
wget -qO- https://raw.githubusercontent.com/creationix/nvm/v0.34.0/install.sh | bash
source ~/.profile
nvm ls-remote
nvm install 14.3.0
node -v
npm -v

curl -sS https://dl.yarnpkg.com/debian/pubkey.gpg | sudo apt-key add -
echo "deb https://dl.yarnpkg.com/debian/ stable main" | sudo tee /etc/apt/sources.list.d/yarn.list
sudo apt update
sudo apt install yarn

cd ~/raau01-backend
php artisan migrate --seed
php artisan storage:link
npm run production
phpunit
```

Add cronjob
Run commands below:
```sh
crontab -e
```

Add this content
```sh
* * * * * cd /PATH_TO_ROOT_DIRECTORY/raau01 && php artisan schedule:run >> /dev/null 2>&1
```
