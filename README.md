# raau01 project
```
http://raau01.test/admin
Username/Password: admin@raau01.dev/ChangeNextTime123@

http://raau01.test/
```

### Installation
#### 1. Docker
Pull laradock folder as below:
```sh
- projects
    -- laradock
    -- raau01-backend
```

```sh
git clone https://github.com/Laradock/laradock.git
cd laradock
cp env-example .env
```
Open .env file and set the following:
```sh
vi .env
```
Edit content as below:
```sh
PHP_VERSION=8.0
WORKSPACE_INSTALL_NODE=true
WORKSPACE_INSTALL_YARN=true
WORKSPACE_INSTALL_IMAGE_OPTIMIZERS=true
WORKSPACE_INSTALL_IMAGEMAGICK=true
WORKSPACE_INSTALL_MYSQL_CLIENT=true
WORKSPACE_TIMEZONE=Asia/Tokyo

PHP_FPM_INSTALL_MYSQLI=true
PHP_FPM_INSTALL_IMAGEMAGICK=true
PHP_FPM_INSTALL_IMAGE_OPTIMIZERS=true
PHP_FPM_INSTALL_MYSQL_CLIENT=true

MYSQL_VERSION=latest
MYSQL_DATABASE=raau01
MYSQL_USER=default
MYSQL_PASSWORD=secret

REDIS_PORT=your_port
REDIS_PASSWORD=your_pass
```
Edit nginx config:
```sh
cd laradock/nginx/sites
touch raau01.conf
```

Add nginx config:
```sh
vi raau01.conf
```
Add content:
```sh
server {
    listen 80;
    listen [::]:80;
    server_name raau01.test;
    root /var/www/raau01-backend/public;
    index index.php index.html index.htm;
    location / {
         try_files $uri $uri/ /index.php$is_args$args;
    }
    location ~ \.php$ {
        try_files $uri /index.php =404;
        fastcgi_pass php-upstream;
        fastcgi_index index.php;
        fastcgi_buffers 16 16k;
        fastcgi_buffer_size 32k;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        #fixes timeouts
        fastcgi_read_timeout 600;
        include fastcgi_params;
    }
    location ~ /\.ht {
        deny all;
    }
    location /.well-known/acme-challenge/ {
        root /var/www/letsencrypt/;
        log_not_found off;
    }
    error_log /var/log/nginx/raau01_error.log;
    access_log /var/log/nginx/raau01_access.log;
}
```

Edit host file:
```sh
sudo vi /etc/hosts
```

Add content:
```sh
127.0.0.1 raau01.test
```

#### 2. Source code:
Clone code:
```sh
git clone https://gitlab.dehasoft.vn/dehasoft/raau01-backend.git
cd raau01-backend
cp .env.example .env
```

Config Laravel-echo-server:
```sh
cp laravel-echo-server.example laravel-echo-server.json

laravel-echo-server.json:
"clients": [
        {
            "appId": "your_appId",
            "key": "your_key"
        }
    ],

env:
MIX_ECHO_SERVER_HOST=laravel-echo-server_host
LARAVEL_ECHO_CLIENT_KEY=copy_clients_key
LARAVEL_ECHO_SERVER_PORT=your_port
LARAVEL_ECHO_SERVER_PROTO=http_or_https
```

Run docker:
```sh
cd laradock
docker-compose up -d mysql nginx workspace redis
```

Open workspace:
```sh
docker-compose exec workspace bash
cd /var/wwww/raau01-backend
yarn
```

Build vendor

```sh
docker-compose exec workspace bash
cd /var/wwww/raau01-backend
composer install
php artisan migrate --seed
npm run production
phpunit
php artisan storage:link
```

Add cronjob

```sh
crontab -e
```
Add content as below:
```sh
* * * * * cd /var/www/raau01-backend && php artisan schedule:run >> /dev/null 2>&1
```

Run Laravel-echo-server:
```sh
cd raau01-backend
npx laravel-echo-server start
```

[Read more here](https://github.com/tlaverdure/laravel-echo-server)

Escrow for production:
```sh
.env:
ESCROW_BASE_URL=https://api.escrow.com
ESCROW_DEFAULT_PATH=/2017-09-01
ESCROW_PAY_PATH=/integration/pay/2018-03-31
ESCROW_EMAIL=your_escrow_email
ESCROW_API_KEY=your_escrow_api_key
ESCROW_WEBHOOK_VERIFICATION_KEY=random_a_string
```

Escrow for develop:
```sh
.env:
ESCROW_BASE_URL=https://api.escrow-sandbox.com
Rest set up same production environment
```

Run command first setup:
```sh
php artisan escrow:register-webhook
```

[Read more here Escrow docs](https://www.escrow.com/pay/docs)
