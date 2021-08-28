<?php

define('CUSTOM_DIR', '../../../custom/events/');
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
try {
    SmartHome\Sensors::storeEvents($uid, $events, $ts);
} catch (\Exception $ex) {
    syslog(LOG_ERR, 'Ошибка при сохранении данных с датчиков:'.PHP_EOL.$ex);
}
if (file_exists(CUSTOM_DIR.$uid.'.php')) {
    chdir(CUSTOM_DIR);
    $eventsListener=require $uid.'.php';
    $eventsListener->uid=$uid;
    foreach ($events as $event=> $value) {
        $method='on_event_'.str_replace('@', '_', $event);
        if (method_exists($eventsListener, $method)) {
            $eventsListener->$method($value, $ts);
        }
    }
}
