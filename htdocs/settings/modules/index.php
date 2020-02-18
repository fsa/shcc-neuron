<?php

require_once '../../common.php';
Auth\Internal::grantAccess(['admin']);
$action=filter_input(INPUT_GET,'action');
if($action) {
    $id=filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
    if(!$id) {
        throw new AppException('Не указан номер модуля');
    }
    switch ($action) {
        case 'enable':
            $name=\SmartHome\Modules::disableModule($id, false);
            HTML::storeNotification('Включение модуля', 'Модуль '.$name.' включен');
            httpResponse::redirect('./');
        case 'disable':
            \SmartHome\Modules::disableModule($id, true);
            $name=HTML::storeNotification('Выключение модуля', 'Модуль '.$name.' выключен');
            httpResponse::redirect('./');
    }
    throw new AppException('Неизвестный тип действия');
}
HTML::showPageHeader('Модули');
?>
<p><a href="../">Вернуться в настройки</a></p>
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
HTML::showPageFooter();