<?php

if (!$param) {
    die;
}
if(!($obj instanceof SmartHome\SensorsInterface)) {
    throw new AppException('Устройство не имеет интерфейса сенсоров');
}
$meters=$obj->getDeviceMeters();
$indicators=$obj->getDeviceIndicators();
if(isset($meters[$param])) {
    echo 'Аналоговый датчик';
    die;
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
            throw new AppException('Данный датчик уже зарегистрирован в базе');
        }
        throw $ex;
    }
    HTML::showNotification('Цифровой датчик '.$param,'Датчик добавлен','./?id='.$id);
    exit;
}
HTML::showNotification('Добавление датчика','Указанного параметра нет ни в списке аналоговых датчиков, ни в цифровых.');