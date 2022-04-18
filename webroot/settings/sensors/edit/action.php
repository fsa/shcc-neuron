<?php
if (!isset($action)) {
    die;
}
$entity = SmartHome\Entity\Sensor::getEntity(App::sql(), 'id');
$entity->inputPostString('uid');
$entity->inputPostString('description');
$entity->inputPostString('property');
$entity->inputPostString('device_property');
$entity->inputPostString('history');
switch ($action) {
    case 'create':
        $entity->insert();
        App::response()->storeNotification('Датчик создан');
        App::response()->redirection('../');
        break;
    case 'edit':
        $entity->update();
        App::response()->storeNotification('Данные о датчике обновлены');
        App::response()->redirection('../');
        break;
}
App::response()->returnError(400, 'Неизвестное действие');
