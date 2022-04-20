<?php

define('CUSTOM_DIR', '../../../custom/');
App::init();
require_once '../../../vendor/autoload.php';
if (getenv('REMOTE_ADDR')!='127.0.0.1') {
    die('Wrong host');
}
fastcgi_finish_request();
if (file_exists(CUSTOM_DIR.'minutely.php')) {
    chdir(CUSTOM_DIR);
    require 'minutely.php';
}