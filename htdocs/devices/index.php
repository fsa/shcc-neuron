<?php

require_once '../common.php';
$module=filter_input(INPUT_GET,'module');
$id=filter_input(INPUT_GET,'id');
if($module) {
    if($id) {
        require_once 'show.php';
        exit;
    }
    die;
}
HTML::showPageHeader('Список устройств в памяти');
$devices=new SmartHome\DeviceList();
foreach ($devices->getModuleList() as $module) {
    $devices->query($module);
    $devices->setFetchClass(Devices::class);
    $table=new Table();
    $table->setCaption('Модуль '.$module);
    $table->addField('id','ID');
    $table->addField('name','Наименование');
    $table->addField('status','Информация');
    $table->addField('sensors','Датчики');
    $table->addField('updated','Был активен');
    $table->addButton('Подробности','./?module='.$module.'&id=%s');
    $table->showTable($devices);
}
HTML::showPageFooter();

class Devices {
    public function __get($prop) {
        switch ($prop) {
            case "sensors":
                if ($this->obj instanceof \SmartHome\SensorsInterface) {
                    $result=[];
                    $analog=join(', ',$this->obj->getDeviceMeters());
                    $digital=join(', ',$this->obj->getDeviceIndicators());
                    if($analog) {
                        $result[]='Аналоговые датчики: '.$analog;
                    }
                    if($digital) {
                        $result[]='Цифровые датчики: '.$digital;
                    }
                    return join('<br>',$result);
                } else {
                    return "Нет датчиков.";
                }
                break;
            default:
                return "Ошибка!";
        }
    }

}
