<?php

if (!isset($hwid)) {
    die;
}
$sh=new SmartHome\MemoryStorage;
$mem_device=$sh->getDevice($hwid);
$devices=new SmartHome\Devices;
list($module,$id)=explode('_', $hwid, 2);
$devices->fetchDeviceByUid($module,$id);
$device=$devices->getDevice();
if (!$device) {
    if($mem_device) {
        httpResponse::redirection("../edit/?hwid=$hwid");
        exit;
    } else {
        httpResponse::showError("Устройство с идентификатором '$hwid' не найдено.");
    }
}
httpResponse::showHtmlHeader();
?>
<p><a href="./">Вернуться к списку устрйств в памяти</a></p>
<hr>
<?php
if(!$mem_device) {
?>
<p>Устройство не загружено в память.</p>
<?php
}
$tpl=new Templates\SmartHome\DeviceInMemory;
$tpl->show($mem_device);
httpResponse::showHtmlFooter();
