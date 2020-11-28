<?php

/**
 * SHCC 0.7.0-dev
 * 2020-11-28
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
if (!isset($json->hwid)) {
    die('Wrong HWID');
}
if (!isset($json->events)) {
    die('Wrong format');
}
$hwid=$json->hwid;
$uid=SmartHome\Devices::getUidByHwid($hwid);
$events=$json->events;
$ts=isset($json->ts)?$json->ts:null;
if ($uid) {
    foreach ($json->events as $property=> $value) {
        SmartHome\Meters::storeEvent($uid.'@'.$property, $value, $ts);
        SmartHome\Indicators::storeEvent($uid.'@'.$property, $value, $ts);
    }
}
$dir='../../custom/events/';
if ($uid and file_exists($dir.$uid.'.php')) {
    chdir($dir);
    include $uid.'.php';
}