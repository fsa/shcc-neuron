<?php

if (!$param) {
    die;
}
if(!($obj instanceof SmartHome\SensorsInterface)) {
    httpResponse::showError('Устройство не имеет интерфейса сенсоров');
}
$meters=$obj->getDeviceMeters();
$indicators=$obj->getDeviceIndicators();
if(isset($meters[$param])) {
    $meter=new SmartHome\Meters;
    $meter->create();
    $meter->setDeviceId($id);
    $meter->setProperty($param,$meters[$param]);
    try {
        $meter->insert();    
    } catch (PDOException $ex) {
        if($ex->getCode()==23000) {
            httpResponse::showError('Данный датчик уже зарегистрирован в базе');
        }
        throw $ex;
    }
    httpResponse::storeNotification('Добавлен аналоговый датчик '.$param);
    httpResponse::redirection('./?id='.$id);
}
if(isset($indicators[$param])) {
    $indicator=new \SmartHome\Indicators;
    $indicator->create();
    $indicator->setDeviceId($id);
    $indicator->setProperty($param);
    try {
        $indicator->insert();    
    } catch (PDOException $ex) {
        if($ex->getCode()==23000) {
            httpResponse::showError('Данный датчик уже зарегистрирован в базе');
        }
        throw $ex;
    }
    httpResponse::storeNotification('Добавлен цифровой датчик '.$param);
    httpResponse::redirection('./?id='.$id);
}
httpResponse::showNotification('Добавление датчика','Указанного параметра нет ни в списке аналоговых датчиков, ни в цифровых.');