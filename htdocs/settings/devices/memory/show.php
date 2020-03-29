<?php

if (!isset($uid)) {
    die;
}
$sh=new SmartHome\Device\MemoryStorage;
$mem_device=$sh->getDevice($uid);
$devices=new SmartHome\Devices;
list($module,$id)=explode('_', $uid, 2);
$devices->fetchDeviceByUid($module,$id);
$device=$devices->getDevice();
if (!$device) {
    if($mem_device) {
        httpResponse::redirection("../edit/?uid=$uid");
        exit;
    } else {
        httpResponse::showError("Устройство с идентификатором '$uid' не найдено.");
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
