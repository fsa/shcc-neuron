<?php

require_once '../../../common.php';
Auth\Session::grantAccess([]);
$hwid=filter_input(INPUT_GET, 'hwid');
if ($hwid) {
    require_once 'show.php';
    exit;
}
httpResponse::showHtmlHeader('Список устройств в памяти');
?>
<p><a href="../">Вернуться к списку устройств</a></p>
<hr>
<p><a href='../edit/'>Добавить вручную</a></p>
<?php
$db_devices=SmartHome\Devices::getDevicesHwids();
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
$memdevitable->addField('hwid', 'HWID');
$memdevitable->addField('description', 'Описание');
$memdevitable->addField('state', 'Информация');
$memdevitable->addField('updated', 'Было активено');
$memdevitable->addButton(new HTML\ButtonLink('Добавить', './?hwid=%s', 'hwid'));
$memdevitable->showTable($devices);
httpResponse::showHtmlFooter();
