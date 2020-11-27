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
    $sh=new SmartHome\Device\MemoryStorage;
    $memdev=$sh->getDevice($hwid);
    if (is_null($memdev)) {
        httpResponse::showError('Что-то пошло не так. Не найдено устройство в памяти.');
    }
    $init_data_list=$memdev->getInitDataList();
    $device=new SmartHome\Entity\Device;
    $device->hwid=$hwid;
    $device->uid=null;
    $device->description=$memdev->getDescription();
    $device->classname=get_class($memdev);
    $device->setInitData($memdev->getInitDataValues());
    $device->place_id=0;
} else {
    if ($uid) {
        ## ByHwid
        $device=SmartHome\Devices::getDeviceById($id);
        $memdev=new $device->classname;
        $init_data_list=$memdev->getInitDataList();
        if (!$device) {
            httpResponse::showError('Устройство не найдено в базе данных');
        }
    } else {
        $device=new SmartHome\Entity\Device;
        $init_data_list=[];
    }
}
httpResponse::showHtmlHeader("Регистрация оборудования");
if ($hwid) {
    Templates\SmartHome\DeviceInMemory::show($memdev);
}
?>
<form method="POST" action="./">
<?php
Forms::inputString('uid', $device->hwid, 'Уникальное имя устройства в системе:');
Forms::inputString('hwid', $device->hwid, 'Уникальный идентификатор устройства*:');
Forms::inputString('classname', $device->classname, 'Класс устройства*:');
Forms::inputString('description', $device->description, 'Описание:');
$values=$device->getInitData();
foreach ($init_data_list as $param=> $name) {
    Forms::inputString('init['.$param.']', isset($values[$param])?$values[$param]:'', $name);
}
Forms::inputSelect('place_id', $device->place_id, 'Расположение:', \SmartHome\Places::getPlaceListStmt());
?>
<p>Параметры, отмеченные * не следует изменять для автоматически обнаруженных устройств, т.к. это может повлиять на их доступность.</p>
<?php
Forms::submitButton($device->uid?'Сохранить изменения':'Добавить новое устройство', $device->uid?'update':'insert');
?>
</form>
<?php
httpResponse::showHtmlFooter();
