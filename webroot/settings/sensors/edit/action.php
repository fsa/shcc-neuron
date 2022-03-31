<?php
use FSA\Neuron\HttpResponse;
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
        HttpResponse::storeNotification('Датчик создан');
        HttpResponse::redirection('../');
        break;
    case 'edit':
        $entity->update();
        HttpResponse::storeNotification('Данные о датчике обновлены');
        HttpResponse::redirection('../');
        break;
}
HttpResponse::showError('Неизвестное действие');
