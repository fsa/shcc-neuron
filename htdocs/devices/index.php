<?php

require_once '../common.php';
HTML::showPageHeader('Список устройств в памяти');
$mem=new MemoryStorage();
$xiaomi=new Xiaomi\DeviceList();
$table=new Table();
$table->setCaption('Xiaomi');
$table->addField('sid','ID');
$table->addField('name','Наименование');
$table->addField('status_description','Информация');
$table->addField('voltage','Батарея');
$table->addField('updated','Был активен');
$table->showTable($xiaomi);
$yeelight=new Yeelight\DeviceList();
$table=new Table();
$table->setCaption('Yeelight');
$table->addField('id','ID');
$table->addField('name','Наименование');
$table->addField('status_description','Информация');
$table->addField('updated','Был активен');
$table->showTable($yeelight);
HTML::showPageFooter();