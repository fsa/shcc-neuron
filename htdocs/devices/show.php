<?php

if (!isset($module)) {
    die;
}
$sh=new SmartHome\DeviceMemoryStorage;
$memdevices=$sh->getModuleDevices($module);
$mem_device=isset($memdevices[$id])?$memdevices[$id]:null;
$devices=new SmartHome\Devices;
$devices->fetchDeviceByUid($module,$id);
$device=$devices->getDevice();
if (!$device) {
    if($mem_device) {
        HTML::redirect("edit/?module=$module&id=$id");
        exit;
    } else {
        throw new AppException("В модуле '$module' устройство с идентификатором '$id' не найдено.");
    }
}
HTML::showPageHeader();
if(!$mem_device) {
?>
<p>Устройство не загружено в память.</p>
<?php
}
$tpl=new Templates\SmartHome\DeviceInMemory;
$tpl->show($mem_device);
HTML::showPageFooter();
