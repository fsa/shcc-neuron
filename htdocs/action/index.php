<?php

require_once '../common.php';
$host=\Settings::get('daemon-ip');
if(!is_null($host)) {
    var_dump(getenv('REMOTE_ADDR'),$host);
    if(getenv('REMOTE_ADDR')!=$host) {
        die('Wrong host');
    }
}
$module=filter_input(INPUT_GET,'module');
$uid=filter_input(INPUT_GET,'uid');
$json=filter_input(INPUT_GET,'data');
if(!$json) {
    $json=filter_input(INPUT_POST,'data');
}
if(!$module or !$uid or !$json) {
    die('Wrong prarameters');
}
$data=json_decode($json);
if(is_null($data)){
    die('Wrong JSON data');
}
$meters=SmartHome\Sensors::getDeviceMeters($module,$uid);
SmartHome\MeterHistory::addRecords($meters,$data);
$indicators=SmartHome\Sensors::getDeviceIndicators($module,$uid);
SmartHome\IndicatorHistory::addRecords($indicators,$data);
$name=SmartHome\Devices::getUniqueNameByUid($module,$uid);
chdir('../../custom/events/');
if($name and file_exists($name.'.php')) {
    include $name.'.php';
}