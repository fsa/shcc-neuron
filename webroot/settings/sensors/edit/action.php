<?php

use FSA\SmartHome\Entity\Sensor;

if (!isset($action)) {
    die;
}
$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
if ($id) {
    $entity = App::sensorDatabase()->get($id);
} else {
    $entity = new Sensor;
}
$old_uid = $entity->uid;
$entity->uid = filter_input(INPUT_POST, 'uid');
$entity->description = filter_input(INPUT_POST, 'description');
$entity->property = filter_input(INPUT_POST, 'property');
$entity->device_property = filter_input(INPUT_POST, 'device_property');
$entity->history = filter_input(INPUT_POST, 'history');
switch ($action) {
    case 'create':
        App::sensorDatabase()->set(null, $entity);
        $response->storeNotification('Датчик создан');
        $response->redirection('../');
        break;
    case 'edit':
        App::sensorDatabase()->set($entity->id, $entity);
        $response->storeNotification('Данные о датчике обновлены');
        $response->redirection('../');
        break;
}
$response->returnError(400, 'Неизвестное действие');
