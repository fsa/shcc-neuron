<?php

require_once '../../../common.php';
Auth\Session::grantAccess([]);
$uid=filter_input(INPUT_GET, 'uid');
if ($uid) {
    require_once 'show.php';
    exit;
}
httpResponse::showHtmlHeader('Список устройств в памяти');
?>
<p><a href="../">Вернуться к списку устройств</a></p>
<hr>
<p><a href='../edit/'>Добавить вручную</a></p>
<?php
$db_devices=SmartHome\Devices::getDevicesUids();
$mem_list=SmartHome\Device\MemoryStorage::getDevicesUids();
$mem_devices=array_flip($mem_list);
foreach ($db_devices as $db_device) {
    if (isset($mem_devices[$db_device])) {
        unset($mem_list[$mem_devices[$db_device]]);
    }
}
$devices=new SmartHome\Device\MemoryStorage();
$devices->selectDeviceList($mem_list);
$memdevitable=new HTML\Table();
$memdevitable->setCaption('Новые устройства в сети');
$memdevitable->addField('uid', 'ID');
$memdevitable->addField('name', 'Описание');
$memdevitable->addField('status', 'Информация');
$memdevitable->addField('updated', 'Было активено');
$memdevitable->addButton(new HTML\ButtonLink('Добавить', './?uid=%s', 'uid'));
$memdevitable->showTable($devices);
httpResponse::showHtmlFooter();
