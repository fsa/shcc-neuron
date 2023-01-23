<?php

use FSA\Neuron\HTML\Table;

require_once '../../../vendor/autoload.php';
App::initHtml(Templates\PageSettings::class);
App::session()->grantAccess([]);
$modules = App::plugins();
$action = filter_input(INPUT_GET, 'action');
if ($action) {
    $name = filter_input(INPUT_GET, 'name');
    if (!$name) {
        App::response()->returnError(400, 'Не указано имя демона');
    }
    switch ($action) {
        case 'settings':
            App::response()->returnError(404, 'Не реализовано');
    }
    App::response()->returnError(400, 'Неизвестный тип действия');
}
App::response()->showHeader('Модули');
$devices = new Table;
$devices->addField('name', 'Наименование');
$devices->addField('description', 'Описание');
$devices->addField('daemon_onoff', 'Демон');
$devices->addField('settings', 'Настройки');
$devices->setRowCallback(function ($row) use ($modules) {
    $row->daemon_onoff = isset($row->daemon) ? 'shcc@' . $row->name : '---';
    $row->settings = isset($row->settings) ? "<a href=\"./?action=settings&name={$row->name}\">Настройки</a>" : '---';
});
$modules->query();
$devices->showTable($modules);
App::response()->showFooter();
