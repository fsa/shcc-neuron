<?php

if(!isset($action)) {
    die;
}
$entity=SmartHome\Entity\Meter::getEntity('id');
$entity->inputPostString('uid');
$entity->inputPostString('description');
$entity->inputPostString('unit');
$entity->inputPostString('device_property');
$entity->inputPostCheckbox('history');
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
