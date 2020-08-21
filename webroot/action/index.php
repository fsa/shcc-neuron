<?php

require_once '../common.php';
$host=Settings::get('daemon-ip');
if (!is_null($host)) {
    if (getenv('REMOTE_ADDR')!=$host) {
        die('Wrong host');
    }
}
$uid=filter_input(INPUT_GET, 'uid');
$json=filter_input(INPUT_GET, 'data');
if (!$json) {
    $json=filter_input(INPUT_POST, 'data');
}
if (!$uid or !$json) {
    die('Wrong prarameters');
}
$data=json_decode($json);
if (is_null($data)) {
    die('Wrong JSON data');
}
$timestamp=filter_input(INPUT_GET, 'ts');
if(!$timestamp) {
    $timestamp=null;
}
$meters=SmartHome\Devices::getMeters($uid);
SmartHome\MeterHistory::addRecords($meters, $data, $timestamp);
$indicators=SmartHome\Devices::getIndicators($uid);
SmartHome\IndicatorHistory::addRecords($indicators, $data, $timestamp);
$name=SmartHome\Devices::getUniqueNameByUid($uid);
$dir='../../custom/events/';
if ($name and file_exists($dir.$name.'.php')) {
    chdir($dir);
    include $name.'.php';
}