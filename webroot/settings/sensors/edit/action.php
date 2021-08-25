<?php

if(!isset($action)) {
    die;
}
$entity=SmartHome\Entity\Sensor::getEntity('id');
$entity->inputPostString('uid');
$entity->inputPostString('description');
$entity->inputPostString('property');
$entity->inputPostString('device_property');
$entity->inputPostString('history');
switch ($action) {
    case 'create':
        $entity->insert();
        httpResponse::storeNotification('Датчик создан');
        httpResponse::redirection('../');
        break;
    case 'edit':
        $entity->update();
        httpResponse::storeNotification('Данные о датчике обновлены');
        httpResponse::redirection('../');
        break;
}
httpResponse::showError('Неизвестное действие');
