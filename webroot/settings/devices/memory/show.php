<?php

/**
 * SHCC 0.7.0
 * 2020-12-25
 */

if (!isset($hwid)) {
    die;
}
$sh=new SmartHome\MemoryStorage;
$mem_device=$sh->getDevice($hwid);
if($mem_device) {
    httpResponse::redirection("../edit/?hwid=$hwid");
} else {
    httpResponse::showError("Устройство с идентификатором '$hwid' не найдено.");
}
