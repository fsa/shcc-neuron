<?php

require_once '../../../common.php';
Auth\Session::grantAccess([]);
$uid=filter_input(INPUT_GET,'uid');
if($uid) {
    require_once 'show.php';
    exit;
}
httpResponse::showHtmlHeader('Список устройств в памяти');
?>
<p><a href="../">Вернуться к списку устройств</a></p>
<hr>
<p><a href='../edit/'>Добавить вручную</a></p>
<?php
$devices=new SmartHome\Device\MemoryStorage();
$devices->selectDevices();
$table=new HTML\Table();
$table->setCaption('Обнаруженные устройства');
$table->addField('uid','ID');
$table->addField('name','Наименование');
$table->addField('status','Информация');
$table->addField('updated','Был активен');
$table->addButton(new HTML\ButtonLink('Подробности','./?uid=%s','uid'));
$table->showTable($devices);
httpResponse::showHtmlFooter();
