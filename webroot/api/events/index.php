<?php

require_once '../../common.php';
$host=Settings::get('daemon-ip', '127.0.0.1');
if (!is_null($host)) {
    if (getenv('REMOTE_ADDR')!=$host) {
        die('Wrong host');
    }
}
$request=file_get_contents('php://input');
file_put_contents('json_'.date('Y_m_d').'.txt', print_r($request, true).PHP_EOL, FILE_APPEND | LOCK_EX);
$json=json_decode($request);
if(!$json) {
    die('Wrong JSON');
}
if(!isset($json->hwid)) {
    die('Wrong HWID');
}
if(!isset($json->events)) {
    die('Wrong format');
}
$hwid=$json->hwid;
$uid=SmartHome\Devices::getUidByHwid($hwid);
$events=$json->events;
$ts=isset($json->ts)?$json->ts:null;
if($uid) {
    foreach ($json->events as $property=>$value) {
        SmartHome\Meters::storeEvent($uid.'@'.$property, $value, $ts);
        file_put_contents(date('Y_m_d').'.txt', print_r([$uid.'@'.$property, $value, $ts], true).PHP_EOL, FILE_APPEND | LOCK_EX);
    }
}
$dir='../../custom/events/';
if ($uid and file_exists($dir.$uid.'.php')) {
    chdir($dir);
    include $uid.'.php';
}