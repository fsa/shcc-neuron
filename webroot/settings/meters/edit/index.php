<?php
/**
 * SHCC 0.7.0
 * 2020-12-24
 */
use Templates\Forms;

require_once '../../../common.php';
Auth\Session::grantAccess([]);
$action=filter_input(INPUT_POST,'action');
if($action) {
    require 'action.php';
    exit;
}
$sensor=SmartHome\Entity\Meter::fetch(filter_input(INPUT_GET, 'id'));
httpResponse::showHtmlHeader($sensor?'Редактировать датчик '.$sensor->id:'Создать новый датчик');
if(!$sensor) {
    $sensor=new SmartHome\Entity\Meter;
}
Forms::formHeader('POST', './');
Forms::inputHidden('id', $sensor->id);
Forms::inputString('uid', $sensor->uid, 'UID - уникальный идентификатор для обращения к датчику');
Forms::inputString('description', $sensor->description, 'Описание');
$units=SmartHome\Meters::getUnits();
foreach ($units as $unit=>$name) {
    $units[$unit]=$name[0].', '.$name[1].' ('.$unit.')';
}
Forms::inputSelectArray('unit', $sensor->unit, 'Единица измерения', $units);
Forms::inputString('device_property', $sensor->device_property, 'Связанное свойство на устройствах');
Forms::inputCheckbox('history', $sensor->history, 'Сохранять данные с датчика');
Forms::submitButton($sensor->id?'Изменить':'Создать', $sensor->id?'edit':'create');
Forms::formFooter();
httpResponse::showHtmlFooter();