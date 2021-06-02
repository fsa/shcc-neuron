<?php

/**
 * SHCC 0.7.0
 * 2020-11-28
 */
define('CUSTOM_DIR', '../../../custom/events/');
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
    if (file_exists(CUSTOM_DIR.$uid.'.php')) {
        chdir(CUSTOM_DIR);
        $eventsListener=require $uid.'.php';
        $eventsListener->uid=$uid;
        foreach ($events as $event=> $value) {
            $method='on_event_'.$event;
            if (method_exists($eventsListener, $method)) {
                $eventsListener->$method($value, $ts);
            }
        }
    }
}
