<?php

require_once '../common.php';
$module=filter_input(INPUT_GET,'module');
$id=filter_input(INPUT_GET,'id');
if($module) {
    if($id) {
        require_once 'show.php';
        exit;
    }
    die;
}
HTML::showPageHeader('Список устройств в памяти');
$devices=new SmartHome\DeviceList();
foreach ($devices->getModuleList() as $module) {
    $devices->query($module);
    $table=new Table();
    $table->setCaption('Модуль '.$module);
    $table->addField('id','ID');
    $table->addField('name','Наименование');
    $table->addField('status','Информация');
    $table->addField('updated','Был активен');
    $table->addButton('Подробности','./?module='.$module.'&id=%s');
    $table->showTable($devices);
}
HTML::showPageFooter();
