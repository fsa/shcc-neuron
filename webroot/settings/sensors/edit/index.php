<?php
use Templates\Forms,
    FSA\Neuron\HttpResponse,
    FSA\Neuron\Session;

require_once '../../../common.php';
Session::grantAccess([]);
$action=filter_input(INPUT_POST,'action');
if($action) {
    require 'action.php';
    exit;
}
$sensor=SmartHome\Entity\Sensor::getEntity('id', INPUT_GET);
HttpResponse::showHtmlHeader($sensor->id?'Редактировать датчик '.$sensor->id:'Создать новый датчик');
Forms::formHeader('POST', './');
Forms::inputHidden('id', $sensor->id);
Forms::inputString('uid', $sensor->uid, 'UID - уникальный идентификатор для обращения к датчику');
Forms::inputString('description', $sensor->description, 'Описание');
$properties=SmartHome\Sensors::getProperties();
foreach ($properties as $property=>$name) {
    $properties[$property]=$name[0].', '.$name[1].' ('.$property.')';
}
Forms::inputSelectArray('property', $sensor->property, 'Единица измерения', $properties);
Forms::inputString('device_property', $sensor->device_property, 'Связанное свойство на устройствах');
Forms::inputString('history', $sensor->history, 'Сохранять данные с датчика в таблице БД');
Forms::submitButton($sensor->id?'Изменить':'Создать', $sensor->id?'edit':'create');
Forms::formFooter();
HttpResponse::showHtmlFooter();