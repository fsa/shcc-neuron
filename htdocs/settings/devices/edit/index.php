<?php
require_once '../../../common.php';
Auth\Internal::grantAccess(['admin']);
$action=filter_input(INPUT_POST,'action');
if($action) {
    require 'edit.php';
    exit;
}
use Templates\Forms;
$id=filter_input(INPUT_GET,'id');
$uid=filter_input(INPUT_GET,'uid');
if($uid) {
    $sh=new SmartHome\Device\MemoryStorage;
    $memdev=$sh->getDevice($uid);
    if(is_null($memdev)) {
        throw new AppException('Что-то пошло не так. Не найдено устройство в памяти.');
    }
    $init_data_list=$memdev->getInitDataList();
    $device=new SmartHome\Entity\Device;
    $device->module_id=$memdev->getModuleName();
    $device->uid=$uid;
    $device->unique_name=$uid;
    $device->description=$memdev->getDeviceDescription();
    $device->classname=get_class($memdev);
    $device->setInitData($memdev->getInitDataValues());
    $device->place_id=0;
} else {
    if($id) {
        $device=SmartHome\Devices::getDeviceById($id);
        $memdev=new $device->classname;
        $init_data_list=$memdev->getInitDataList();
        if(!$device) {
            throw new AppException('Устройство не найдено в базе данных');
        }
    } else {
        $device=new SmartHome\Entity\Device;
        $init_data_list=[];
    }
}
HTML::showPageHeader("Регистрация оборудования '$uid'");
if($uid) {
?>
<p>Для работы с новым устройством его необходимо добавить в базу данных.</p>
<?php
    Templates\SmartHome\DeviceInMemory::show($memdev);
}
?>
<form method="POST" action="./">
<?php
Forms::inputString('unique_name',$device->unique_name,'Уникальное имя устройства:');
Forms::inputHidden('id',$device->id);
Forms::inputSelect('module_id',$device->module_id,'Модуль*:',\SmartHome\Modules::getModuleList());
Forms::inputString('uid',$device->uid,'Аппаратное ID устройства*:');
Forms::inputString('classname',$device->classname,'Класс устройства*:');
Forms::inputString('description',$device->description,'Описание:');
$values=$device->getInitData();
foreach ($init_data_list as $param=>$name) {
    Forms::inputString('init['.$param.']',isset($values[$param])?$values[$param]:'',$name);    
}
Forms::inputSelect('place_id',$device->place_id,'Расположение:',\SmartHome\Places::getPlaceListStmt());
Forms::inputCheckbox('disabled',$device->disabled,'Выключить');
?>
<p>Параметры, отмеченные * не следует изменять для автоматически обнаруженных устройств, т.к. это может повлиять на их доступность.</p>
<?php
Forms::submitButton($device->id?'Сохранить':'Создать',$device->id?'update':'insert');
?>
</form>
<?php
HTML::showPageFooter();