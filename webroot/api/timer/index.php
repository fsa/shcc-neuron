<?php

require_once '../../../vendor/autoload.php';
App::init();
if (getenv('REMOTE_ADDR') != '127.0.0.1') {
    die('Wrong host');
}
#fastcgi_finish_request();
$work_dir = App::getWorkDir() . 'custom/';
if (file_exists($work_dir . 'minutely.php')) {
    chdir($work_dir);
    require 'minutely.php';
}
