<?php

use FSA\SmartHome\Entity\Sensor;
use FSA\SmartHome\Sensors;
use Templates\Forms;

require_once '../../../../vendor/autoload.php';
$response=App::initHtml();
App::session()->grantAccess([]);
$action=filter_input(INPUT_POST,'action');
if($action) {
    require 'action.php';
    exit;
}

$id = filter_input(INPUT_GET, 'id');
if ($id) {
    $sensor=App::sensorDatabase()->get($id);
    if (!$sensor) {
        $response->returnError(404, 'Датчик не найден');
    }
} else {
    $sensor=new Sensor;
}
$response->showHeader($sensor->id?'Редактировать датчик '.$sensor->id:'Создать новый датчик');
Forms::formHeader('POST', './');
Forms::inputHidden('id', $sensor->id);
Forms::inputString('uid', $sensor->uid, 'UID - уникальный идентификатор для обращения к датчику');
Forms::inputString('description', $sensor->description, 'Описание');
$properties=Sensors::getProperties();
foreach ($properties as $property=>$name) {
    $properties[$property]=$name[0].', '.$name[1].' ('.$property.')';
}
Forms::inputSelectArray('property', $sensor->property, 'Единица измерения', $properties);
Forms::inputString('device_property', $sensor->device_property, 'Связанное свойство на устройствах');
Forms::inputString('history', $sensor->history, 'Сохранять данные с датчика в таблице БД');
Forms::submitButton($sensor->id?'Изменить':'Создать', $sensor->id?'edit':'create');
Forms::formFooter();
$response->showFooter();