<?php

require_once '../common.php';
HTML::showPageHeader('Список устройств в памяти');
$devices=new SmartHome\DeviceList();
foreach ($devices->getModuleList() as $module) { 
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
