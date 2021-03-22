<?php

require_once '../../common.php';
Auth\Session::grantAccess([]);
$modules=new SmartHome\Modules;
$action=filter_input(INPUT_GET, 'action');
if ($action) {
    $name=filter_input(INPUT_GET, 'name');
    if (!$name) {
        httpResponse::showError('Не указано имя демона');
    }
    switch ($action) {
        case 'enable':
            $modules->enableDaemon($name);
            httpResponse::storeNotification('Демон модуля '.$name.' будет включен при следующем запуске сервиса SHCC.');
            httpResponse::redirection('./');
        case 'disable':
            $modules->disableDaemon($name);
            httpResponse::storeNotification('Демон модуля '.$name.' будет выключен при следующем запуске сервиса SHCC.');
            httpResponse::redirection('./');
    }
    httpResponse::showError('Неизвестный тип действия');
}
httpResponse::setTemplate(new Templates\PageSettings);
httpResponse::showHtmlHeader('Модули');
$devices=new HTML\Table;
$devices->addField('name', 'Наименование');
$devices->addField('description', 'Описание');
$devices->addField('daemon_onoff', 'Демон');
$devices->addField('settings', 'Настройки');
$devices->setRowCallback(function ($row) use ($modules) {
    $row->daemon_onoff=class_exists($row->daemon)?($modules->isDaemonActive(strtolower($row->name))?'<a href="./?action=disable&name='.strtolower($row->name).'">Выключить</a>':'<a href="./?action=enable&name='.strtolower($row->name).'">Включить</a>'):'---';
    $row->settings=isset($row->settings)?'<a href="'.strtolower($row->name).'/">Настроить</a>':'---';
});
$modules->query();
$devices->showTable($modules);
httpResponse::showHtmlFooter();
