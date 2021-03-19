<?php

/**
 * SHCC 0.7.0
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
Logger::debug('events', $request);
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
fastcgi_finish_request();
$hwid=$json->hwid;
$events=$json->events;
$ts=isset($json->ts)?$json->ts:null;
$uid=SmartHome\Devices::storeEvents($hwid, $events, $ts);
if ($uid) {
    $dir='../../../custom/events/';
    if (file_exists($dir.$uid.'.php')) {
        chdir($dir);
        include $uid.'.php';
    }
}
