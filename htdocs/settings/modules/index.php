<?php

require_once '../../common.php';
Auth\Session::grantAccess([]);
$action=filter_input(INPUT_GET,'action');
if($action) {
    $id=filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
    if(!$id) {
        httpResponse::showError('Не указан номер модуля');
    }
    switch ($action) {
        case 'enable':
            $name=\SmartHome\Modules::disableModule($id, false);
            httpResponse::storeNotification('Модуль '.$name.' включен.');
            httpResponse::redirection('./');
        case 'disable':
            \SmartHome\Modules::disableModule($id, true);
            $name=httpResponse::storeNotification('Модуль '.$name.' выключен.');
            httpResponse::redirection('./');
    }
    httpResponse::showError('Неизвестный тип действия');
}
httpResponse::showHtmlHeader('Модули');
httpResponse::showNavPills('../%s/', require '../sections.php', 'modules');
?>
<hr>
<?php
$devices=new HTML\Table;
$devices->addField('name','Наименование');
$devices->addField('namespace', 'Пространство имён');
$devices->addField('description', 'Описание');
$devices->addField('daemon', 'Демон');
$devices->addField('settings', 'Настройки');
$devices->addField('disabled', 'Активность');
$devices->setRowCallback(function ($row) {
    $row->daemon=$row->daemon==true?'Есть':'Нет';
    $row->settings=$row->settings?'<a href="'.$row->name.'/">Есть</a>':'Нет';
    $row->disabled=$row->disabled?'<a href="?action=enable&id='.$row->id.'">Включить</a>':'<a href="?action=disable&id='.$row->id.'">Выключить</a>';
});
$devices->showTable(\SmartHome\Modules::getModules());
httpResponse::showHtmlFooter();
