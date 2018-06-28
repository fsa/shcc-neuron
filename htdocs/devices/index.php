<?php

require_once '../common.php';
HTML::showPageHeader('Список устройств в памяти');
$mem=new \SmartHome\DeviceMemoryStorage;
$modules=$mem->getModuleList();
foreach ($modules as $module) {
    $devices=new SmartHome\DeviceList();
    $devices->query($module);
    $table=new Table();
    $table->setCaption('Модуль '.$module);
    $table->addField('id','ID');
    $table->addField('name','Наименование');
    $table->addField('status_description','Информация');
    $table->addField('updated','Был активен');
    $table->showTable($devices);
}
HTML::showPageFooter();
