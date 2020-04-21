<?php

require_once '../../common.php';
Auth\Session::grantAccess([]);
$action=filter_input(INPUT_GET,'action');
if($action) {
    $name=filter_input(INPUT_GET, 'name');
    if(!$name) {
        httpResponse::showError('Не указано имя демона');
    }
    switch ($action) {
        case 'enable':
#            SmartHome\Daemons::enable($name);
            httpResponse::storeNotification('Демон модуля '.$name.' будет включен при следующем запуске сервиса SHCC.');
            httpResponse::redirection('./');
        case 'disable':
#            SmartHome\Daemons::disable($name);
            httpResponse::storeNotification('Демон модуля '.$name.' будет выключен при следующем запуске сервиса SHCC.');
            httpResponse::redirection('./');
    }
    httpResponse::showError('Неизвестный тип действия');
}
httpResponse::showHtmlHeader('Модули');
httpResponse::showNavPills('../%s/', require '../sections.php', 'modules');
?>
<hr>
<?php
$active_daemons=SmartHome\Daemons::getActive();
$devices=new HTML\Table;
$devices->addField('name','Наименование');
$devices->addField('description', 'Описание');
$devices->addField('daemon', 'Демон');
$devices->addField('settings', 'Настройки');
$devices->setRowCallback(function ($row) use ($active_daemons) {
    $row->daemon=class_exists($row->name.'\\Daemon')?(array_search($row->name.'\\Daemon', $active_daemons)===false?'<a href="./?action=enable&name='.$row->name.'">Включить</a>':'<a href="./?action=disable&name='.$row->name.'">Выключить</a>'):'---';
    $row->settings=isset($row->settings)?'<a href="'.strtolower($row->name).'/">Настроить</a>':'---';
});
$devices->showTable(new SmartHome\Modules);
httpResponse::showHtmlFooter();
