<?php
if (!isset($hwid)) {
    die;
}
$sh=new SmartHome\DeviceStorage;
$mem_device=$sh->get($hwid);
if($mem_device) {
    httpResponse::redirection("../edit/?hwid=$hwid");
} else {
    httpResponse::showError("Устройство с идентификатором '$hwid' не найдено.");
}
