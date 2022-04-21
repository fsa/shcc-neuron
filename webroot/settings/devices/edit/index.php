<?php

use Templates\Forms;

require_once '../../../../vendor/autoload.php';
App::initHtml(Templates\PageSettings::class);
App::session()->grantAccess([]);
$action=filter_input(INPUT_POST, 'action');
if ($action) {
    require 'edit.php';
    exit;
}
$uid=filter_input(INPUT_GET, 'uid');
$hwid=filter_input(INPUT_GET, 'hwid');
if ($hwid) {
    $sh=new SmartHome\DeviceStorage;
    $memdev=$sh->get($hwid);
    if (is_null($memdev)) {
        App::response()->returnError(500, 'Что-то пошло не так. Не найдено устройство в памяти.');
    }
    $init_data_list=$memdev->getInitDataList();
    $device=new SmartHome\Entity\Device;
    $device->uid=$hwid;
    $device->hwid=$hwid;
    $device->description=$memdev->getDescription();
    $device->setInitData($memdev->getInitDataValues());
    $classname=get_class($memdev);
} else {
    if ($uid) {
        $device=SmartHome\Devices::getDeviceByUid($uid);
        if (!$device) {
            App::response()->returnError(500, 'Устройство не найдено в базе данных');
        }
        $entity=json_decode($device->entity);
        $classname = $entity->classname;
        if(class_exists($classname)) {
            $memdev=new $classname;
            $init_data_list=$memdev->getInitDataList();
            
        } else {
            $memdev=null;
            $init_data_list = [];
        }
    } else {
        $device=new SmartHome\Entity\Device;
        $init_data_list=[];
        $classname='';
    }
}
App::response()->showHeader("Регистрация оборудования");
if ($hwid) {
    Templates\SmartHome\DeviceInMemory::show($memdev);
}
?>
<form method="POST" action="./">
<?php
if($uid) {
    Forms::inputHidden('old_uid', $device->uid);
}
Forms::inputString('uid', $device->uid, 'Уникальное имя устройства в системе:');
Forms::inputString('hwid', $device->hwid, 'Уникальный идентификатор устройства*:');
Forms::inputString('classname', $classname, 'Класс устройства*:');
Forms::inputString('description', $device->description, 'Описание:');
$values=$device->getInitData();
foreach ($init_data_list as $param=> $name) {
    Forms::inputString('properties['.$param.']', isset($values['properties'][$param])?$values['properties'][$param]:'', $name);
}
?>
<p>Параметры, отмеченные * не следует изменять для автоматически обнаруженных устройств, т.к. это может повлиять на их доступность.</p>
<?php
Forms::submitButton($uid?'Сохранить изменения':'Добавить новое устройство', $uid?'update':'insert');
?>
</form>
<?php
App::response()->showFooter();
