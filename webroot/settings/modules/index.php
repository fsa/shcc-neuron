<?php
use FSA\Neuron\HttpResponse,
    FSA\Neuron\Session,
    FSA\Neuron\HTML\Table;
require_once '../../common.php';
Session::grantAccess([]);
$modules=new SmartHome\Modules;
$action=filter_input(INPUT_GET, 'action');
if ($action) {
    $name=filter_input(INPUT_GET, 'name');
    if (!$name) {
        HttpResponse::showError('Не указано имя демона');
    }
    switch ($action) {
        case 'enable':
            $modules->enableDaemon($name);
            HttpResponse::storeNotification('Демон модуля '.$name.' будет включен при следующем запуске сервиса SHCC.');
            HttpResponse::redirection('./');
        case 'disable':
            $modules->disableDaemon($name);
            HttpResponse::storeNotification('Демон модуля '.$name.' будет выключен при следующем запуске сервиса SHCC.');
            HttpResponse::redirection('./');
    }
    HttpResponse::showError('Неизвестный тип действия');
}
HttpResponse::setTemplate(new Templates\PageSettings);
HttpResponse::showHtmlHeader('Модули');
$devices=new Table;
$devices->addField('name', 'Наименование');
$devices->addField('description', 'Описание');
$devices->addField('daemon_onoff', 'Демон');
$devices->addField('settings', 'Настройки');
$devices->setRowCallback(function ($row) use ($modules) {
    $row->daemon_onoff=isset($row->daemon)?($modules->isDaemonActive(strtolower($row->name))?'<a href="./?action=disable&name='.strtolower($row->name).'">Выключить</a>':'<a href="./?action=enable&name='.strtolower($row->name).'">Включить</a>'):'---';
    $row->settings=isset($row->settings)?'<a href="'.strtolower($row->name).'/">Настроить</a>':'---';
});
$modules->query();
$devices->showTable($modules);
HttpResponse::showHtmlFooter();
