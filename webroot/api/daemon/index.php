<?php

/**
 * SHCC 0.7.0
 * 2021-03-19
 */
require_once '../../common.php';
$host=Settings::get('daemon-ip', '127.0.0.1');
if (!is_null($host)) {
    if (getenv('REMOTE_ADDR')!=$host) {
        die('Wrong host');
    }
}
$request=file_get_contents('php://input');
$json=json_decode($request);
if (!$json) {
    die('Wrong JSON');
}
if (!isset($json->module)) {
    die('Wrong HWID');
}
# Инициализация разделяемой памяти
$mem=new SmartHome\MemoryStorage;
$class=SmartHome\Daemons::getClass($json->module);
if($class) {
    httpResponse::json(['daemon'=>true, 'class'=>$class]);
} else {
    if(class_exists($json->module.'\\Daemon')) {
        httpResponse::json(['daemon'=>false]);
    } else {
        httpResponse::json(['daemon'=>null]);
    }
}