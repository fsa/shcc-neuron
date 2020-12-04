<?php
require_once '../../../common.php';
Auth\Session::grantAccess([]);
$action=filter_input(INPUT_POST, 'action');
if ($action) {
    require 'edit.php';
    exit;
}

use Templates\Forms;

$uid=filter_input(INPUT_GET, 'uid');
$hwid=filter_input(INPUT_GET, 'hwid');
if ($hwid) {
    $sh=new SmartHome\MemoryStorage;
    $memdev=$sh->getDevice($hwid);
    if (is_null($memdev)) {
        httpResponse::showError('Что-то пошло не так. Не найдено устройство в памяти.');
    }
    $init_data_list=$memdev->getInitDataList();
    $device=new SmartHome\Entity\Device;
    $device->uid=$hwid;
    $device->hwid=$hwid;
    $device->description=$memdev->getDescription();
    $device->setInitData($memdev->getInitDataValues());
    $device->place_id=0;
    $classname=get_class($memdev);
} else {
    if ($uid) {
        $device=SmartHome\Devices::getDeviceByUid($uid);
        if (!$device) {
            httpResponse::showError('Устройство не найдено в базе данных');
        }
        $entity=json_decode($device->entity);
        $memdev=new $entity->classname;
        $init_data_list=$memdev->getInitDataList();
        $classname=$entity->classname;
    } else {
        $device=new SmartHome\Entity\Device;
        $init_data_list=[];
        $classname='';
    }
}
httpResponse::showHtmlHeader("Регистрация оборудования");
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
Forms::inputSelect('place_id', $device->place_id, 'Расположение:', \SmartHome\Places::getPlaceListStmt());
?>
<p>Параметры, отмеченные * не следует изменять для автоматически обнаруженных устройств, т.к. это может повлиять на их доступность.</p>
<?php
Forms::submitButton($uid?'Сохранить изменения':'Добавить новое устройство', $uid?'update':'insert');
?>
</form>
<?php
httpResponse::showHtmlFooter();
