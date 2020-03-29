<?php

$id=filter_input(INPUT_GET,'id',FILTER_VALIDATE_INT);
if (!$id) {
    die;
}
require_once '../../../common.php';
Auth\Session::grantAccess([]);
$device=SmartHome\Devices::getDeviceById($id);
if (!$device) {
    die;
}
$obj=new $device->classname;
$param=filter_input(INPUT_GET,'param');
if($param) {
    require 'add.php';
    die;
}
httpResponse::showHtmlHeader('Датчики утройств');
if($obj instanceof SmartHome\SensorsInterface) {
    $meters=SmartHome\Meters::getMetersByDeviceId($device->id);
    foreach ($obj->getDeviceMeters() as $name=>$title) {
        echo "<p>$name => $title".(isset($meters[$name])?'':" <a href=\"./?id=$id&param=$name\">Сохранять историю показаний</a>")."</p>";
    }
    $indicators=SmartHome\Indicators::getIndicatorsByDeviceId($device->id);
    foreach ($obj->getDeviceIndicators() as $name=>$title) {
        echo "<p>$name => $title".(isset($indicators[$name])?'':" <a href=\"./?id=$id&param=$name\">Сохранять историю показаний</a>")."</p>";
    }
}
if($obj instanceof SmartHome\DeviceActionInterface) {
    foreach ($obj->getDeviceActions() as $name=>$title) {
        echo "<p>$name => $title</p>";
    }    
}
httpResponse::showHtmlFooter();
