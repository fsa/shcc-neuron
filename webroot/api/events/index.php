<?php

require_once '../../common.php';
$host=Settings::get('daemon-ip', '127.0.0.1');
if (!is_null($host)) {
    if (getenv('REMOTE_ADDR')!=$host) {
        die('Wrong host');
    }
}
$request=file_get_contents('php://input');
syslog(LOG_DEBUG, __FILE__.':'.__LINE__.' Daemon event: '.$request);
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
$uid=SmartHome\Devices::getUidByHwid($json->hwid);
if (!$uid) {
    exit;
}
$events=$json->events;
$ts=isset($json->ts)?$json->ts:null;
SmartHome\Devices::processEvents($uid, $events, $ts);