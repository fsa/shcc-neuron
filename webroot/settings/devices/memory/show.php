<?php
use FSA\Neuron\HttpResponse;
if (!isset($hwid)) {
    die;
}
$sh=new SmartHome\DeviceStorage;
$mem_device=$sh->get($hwid);
if($mem_device) {
    HttpResponse::redirection("../edit/?hwid=$hwid");
} else {
    HttpResponse::showError("Устройство с идентификатором '$hwid' не найдено.");
}
