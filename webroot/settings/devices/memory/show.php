<?php

if (!isset($hwid)) {
    die;
}
$sh=new SmartHome\DeviceStorage;
$mem_device=$sh->get($hwid);
if($mem_device) {
    App::response()->redirection("../edit/?hwid=$hwid");
} else {
    App::response()->returnError(500, "Устройство с идентификатором '$hwid' не найдено.");
}
