# Установка системы

Для установки SHCC подойдёт любой дистрибутив Linux в котором имеются необходимые версии PHP, PostgreSQL и nginx. Кроме этого, требуется использовать систему инициализации systemd, либо обеспечить запуск необходимых сервисов и cron заданий самостоятельно.

Рекомендуемым вариантом установки является Ubuntu 20.04 или Fedora 35, т.к. в состав их репозиториев включены все необходимые компоненты. Все команды для установки системы приведены для Ubuntu.

## Установка PostgreSQL

Для установки сервер PostgreSQL выполните:

```bash
sudo apt install postgresql
```

После установки рекомендуется можно подключиться к серверу и убедиться, что он работает:

```console
# su - postgres
$ psql
psql (10.9)
Type "help" for help.

postgres=# \l
                                  List of databases
    Name    |   Owner    | Encoding |  Collate   |   Ctype    |   Access privileges   
------------+------------+----------+------------+------------+-----------------------
 postgres   | postgres   | UTF8     | ru_RU.utf8 | ru_RU.utf8 | 
 template0  | postgres   | UTF8     | ru_RU.utf8 | ru_RU.utf8 | =c/postgres          +
            |            |          |            |            | postgres=CTc/postgres
 template1  | postgres   | UTF8     | ru_RU.utf8 | ru_RU.utf8 | =c/postgres          +
            |            |          |            |            | postgres=CTc/postgres
(3 rows)
```

## Установка веб-сервера nginx и php

Установите веб-сервер и php с необходимыми модулями с помощью команды:

```bash
sudo apt install nginx php-fpm php-pgsql php-redis
```

Для настройки веб-сервера nginx создайте файл конфигурации виртуального хоста. Для этого необходимо создать файл с содержимым конфигурации в папке `/etc/nginx/sites-available/` и создать на него символическую ссылку в папке `sites-enabled`.

Пример файла конфигурации приведён ниже:

```nginx
server {
    listen 80 default_server;

    # https
    listen 443 ssl http2 default_server;
    ssl_certificate /etc/letsencrypt/live/shcc.example.com/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/shcc.example.com/privkey.pem;

    server_name  shcc.example.com;
    
    charset utf-8;
    
    access_log  /var/log/nginx/shcc_access.log;
    error_log  /var/log/nginx/shcc_error.log;

    root /var/www/shcc/webroot;

    index index.php;

    location ~ \.php$ {
        fastcgi_pass php-fpm;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
        include conf.d/shcc_params;
    }

    location ^~ /alice-webhook {
        fastcgi_pass php-fpm;
        fastcgi_split_path_info ^(\/alice-webhook)(.*)$;
        fastcgi_param SCRIPT_FILENAME $document_root/alice-webhook/index.php;
        fastcgi_param PATH_INFO $fastcgi_path_info;
        include fastcgi_params;
        include conf.d/shcc_params;
    }

    include acme.conf;

}
```

Файл с [переменными окружения](environment.md) `/etc/nginx/conf.d/shcc_params`:

```nginx
fastcgi_param DATABASE_URL "postgres://shcc:PASSWORD@localhost/shcc";
fastcgi_param SESSION_NAME shcc;
fastcgi_param APP_ADMINS my_user;
fastcgi_param TZ Asia/Yekaterinburg;
```

Вместо shcc.example.com укажите свой домен, который вы планируете использовать для доступа к умному дому из сети, в `DATABASE_URL` вместо `PASSWORD` укажите пароль для пользователя shcc БД.

Запуск php производится с помощью upstream php-fpm. Сделать это можно создав файл `/etc/nginx/conf.d/php-fpm.conf`:

```nginx
upstream php-fpm {
    server unix:/run/php/php7.4-fpm.sock;
}
```

Адрес unix-сокета должен совпадать с указанным в файле конфигурации php-fpm, например, `/etc/php/7.4/fpm/pool.d/www.conf` для php версии 7.4 (значение listen). Вы можете использовать и другие способы взаимодействия веб-сервера и php-fpm, например, связь через TCP/IP. В любом случае, настройки nginx и php-fpm должны указывать на один и тот же объект.

Обратите внимание на часть файла после комментария https. Эти команды нужны для использования протокола https и при начальной настройке могут вызвать ошибки при запуске сервера. Чтобы этого избежать закомментируйте директивы listen 443..., ssl_.... После получения сертификатов их можно будет раскомментировать.

Ещё один требуемый файл - `acme.conf`. Если вы не планируете использовать сертификаты от Let's Encrypt можете просто закомментировать include acme.conf; в файле конфигурации виртуального хоста.

Для удобства получения бесплатных сертификатов https создаём файл `/etc/nginx/acme.conf` со следующим содержимым:

```nginx
location /.well-known/acme-challenge {
    default_type "text/plain";
    root /var/www/letsencrypt;
    allow all;
}
```

Также необходимо будет создать папку `/var/www/letsencrypt` и задать для неё владельца и группу веб-сервера (www-data). Эту папку нужно будет указывать в параметрах `certbot` или `acme.sh` при получении сертификата для своего домена.

Для настройки https также можно добавить в секцию http файла конфигурации nginx `/etc/nginx/nginx.conf` параметры ssl. Пример актуального списка параметров конфигурации https на момент написания документации приведён ниже:

```nginx
    ssl_protocols TLSv1.3 TLSv1.2;
    ssl_prefer_server_ciphers on;
    ssl_ciphers 'ECDHE-ECDSA-AES256-GCM-SHA384:ECDHE-RSA-AES256-GCM-SHA384:ECDHE-ECDSA-CHACHA20-POLY1305:ECDHE-RSA-CHACHA20-POLY1305:ECDHE-ECDSA-AES128-GCM-SHA256:ECDHE-RSA-AES128-GCM-SHA256:ECDHE-ECDSA-AES256-SHA384:ECDHE-RSA-AES256-SHA384:ECDHE-ECDSA-AES128-SHA256:ECDHE-RSA-AES128-SHA256';

    ssl_session_timeout 1d;
    ssl_session_cache shared:SSL:50m;
    ssl_session_tickets off;

    ssl_stapling on;
    ssl_stapling_verify on;
```

## Получение SHCC

SHCC рекомендуется размещать в папке `/var/www/shcc`.

Перейдите в папку установки (рекомендуется `/var/www`) и клонируйте репозиторий shcc с github:

```bash
cd /var/www
git clone https://github.com/fsa/shcc
```

При этом будет создана папка shcc.

## Подготовка shcc к запуску

Создаём `settings.php` используя шаблон `settings.sample.php`.

```bash
cp settings.sample.php settings.php
```

Отредактируйте полученный файл указав желаемые настройки.

Для установки всех необходимых пакетов выполните в папке `shcc`

```bash
composer install
```

## Создание базы данных

Воспользуйтесь командной строкой, чтобы создать базу данных и импортировать в неё необходимые таблицы. Для этого переключитесь на пользователя postgres, выберите папку со скриптами инициализации базы данных и запустите psql.

```bash
root@shcc:~# su postgres
postgres@shcc:/root$ cd /var/www/shcc/src/sql
postgres@shcc:/var/www/shcc/src/sql$ psql
```

Создайте пользователя и базу данных для него. Задайте пароль для пользователя.

```console
postgres=# create user shcc;
CREATE ROLE
postgres=# create database shcc owner=shcc;
CREATE DATABASE
postgres=# \password shcc
Enter new password: 
Enter it again:
```

Когда база данных создана можно подключиться к серверу с помощью вновь созданного пользователя к базе данных и инициализировать её с помощью команд импорта:

```console
postgres=# \c shcc shcc 127.0.0.1
Password for user shcc:
SSL connection (protocol: TLSv1.3, cipher: TLS_AES_256_GCM_SHA384, bits: 256, compression: off)
You are now connected to database "shcc" as user "shcc" on host "127.0.0.1" at port "5432".
shcc=> \i user.sql
...
shcc=> \i smarthome.sql
...
```

Завершающим этапом является создание пользователя для доступа к сайту умного дома.

```sql
shcc=> insert into auth_users (login, password, name) values ('user','$2y$10$3BJwk3WNeAtIxd8Gory/TONHAFe.8GkKAM2Afjxdc1njS25a.twbi', 'User');
```

Первое поле, в данном случае - это имя пользователя, которое используется для входа в систему. Второе поле - это хеш пароля, который можно получить с помощью команды password_hash:

```php
<?php
echo password_hash('password', PASSWORD_DEFAULT, ['cost'=>12]);
```

Для запуска скрипта можете воспользоваться сайтом <http://phptester.net/> или запустить скрипт локально.

В примере был использован логин "user" с паролем "123". В целях безопасности лучше придумать свои,

## Конфигурирование модулей

После создания всех необходимых файлов конфигурации и базы данных первая часть системы готова к работе. Вы можете открыть сайт системы в браузере и произвести первоначальную настройку демонов. Для этого перейдите в пункт "Настройки", далее, в раздел "Модули" и включите необходимые вам модули. По умолчанию все демоны модулей находятся в выключенном состоянии.

Кроме этого можно произвести настройку синтеза речи в настройках соответствующих модулей.

## Запуск демонов и ежеминутного скрипта

Для нормальной работы контроллера необходимо запустить несколько процессов:

1. скрипт service/daemon.php должен быть запущен в одном экземпляре дла каждого модуля SHCC, который требует демона;
2. скрипт custom/minutely.php, который выполняет задачи по расписанию и должен запускаться ежеминутно.

### Создание ежеминутного скрипта

В исходных кодах в папке custom имеется пример скрипта. Скопируйте его содержимое в файл minutely.php и отредактируйте полученный файл на ваше усмотрение.

```bash
cp minutely.sample.php minutely.php
```

## Обеспечение автоматического запуска необходимых процессов на системе с systemd

Для запуска демонов и ежеминутного скрипта имеются готовые юнит-файлы для systemd. При стандартном расположении в папке `/var/www/shcc` необходимости настраивать файлы нет. Создайте символические ссылки на файлы service и timer в каталоге пользователя, от которого работает система `~/.config/systemd/user/`.

``` bash
cd /var/www/shcc/service/systemd
mkdir -p ~/.config/systemd/user/
ln -s shcc.target ~/.config/systemd/user/
ln -s shcc@.service ~/.config/systemd/user/
ln -s shcc-minutely.service ~/.config/systemd/user/
ln -s shcc-minutely.timer ~/.config/systemd/user/
```

Теперь можно активировать и запустить требуемые сервисы. Используя учётную запись пользователя выполните:

```bash
systemctl enable --now --user shcc.target
```

Активируйте автоматический запуск юнитов systemd от имени пользователя при старте системы:

```bash
loginctl enable-linger username
```

Если пользователь, который используется для запуска юнитов, никогда не входит в систему (вы переключить на него с использованием su), возможны ошибки при попытки активации автоматического запуска юнитов. Активируйте юниты после использования данной команды.

## Включение голосовых оповещений

Для работы голосовых оповещений необходимо установить проигрыватель. По умолчанию используется mpg123. Указать другой проигрыватель возможно через файл настроек `settings.php`.

```bash
apt install mpg123
```

После установки убедитесь, что звук воспроизводится из командной строки выполнив команду:

```bash
mpg123 sound.mp3
```

при этом указав имя существующего звукового файла.

Поскольку демоны работают от пользователя www-data по умолчанию, то нужно добавить этого пользователя в группу audio, чтобы он имел возможность воспроизводить звук:

```bash
usermod www-data -aG audio
```

Если для запуска демонов вы используете другого пользователя, то убедитесь, что он состоит в данной группе.

### Создание конфигурации для синтезатора речи

Зайдите в веб-интерфейсе в раздел "Настройки", и произведите настройку необходимого модуля синтеза речи. После этого сохраните конфигурацию.

### Включение звука для Raspberry Pi

При установке Ubuntu на Raspberry Pi может оказаться, что звук не воспроизводится. Для его активации необходимо включить звуковой драйвер в файле конфигурации /boot/firmware/usercfg.txt (в старых версиях системы, при его отсутствии, /boot/firmware/config.txt) добавив строку

```ini
dtparam=audio=on
```

Далее выполните перезагрузку системы и звук должен появиться.

## Разрешения для SELinux

Для включения возможности управления устройствами Xiaomi через режим управления по локальной сети необходимо разрешить http серверу работать с портом 9898:

```bash
semanage port -a -t http_port_t -p udp 9898
```
