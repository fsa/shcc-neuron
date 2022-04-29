<?php

use Templates\Forms;

require_once '../../../../vendor/autoload.php';
$response = App::initHtml(Templates\PageSettings::class);
App::session()->grantAccess([]);
$action = filter_input(INPUT_POST, 'action');
if ($action) {
    require 'edit.php';
    exit;
}
$uid = filter_input(INPUT_GET, 'uid');
$hwid = filter_input(INPUT_GET, 'hwid');
if ($hwid) {
    $mem_dev = SmartHome::deviceStorage()->get($hwid);
    if (is_null($mem_dev)) {
        $response->returnError(404, 'Информация об устройстве в памяти не найдена.');
    }
    $hwid_parts = explode(':', $hwid, 2);
    if (sizeof($hwid_parts)!=2) {
        $response->returnError(400, 'Неверный аппаратный идентификатор устройства');
    }
    $init_data_list = $mem_dev->getInitDataList();
    $device = new FSA\SmartHome\Entity\Device;
    $device->uid = $hwid;
    $device->plugin = $hwid_parts[0];
    $device->hwid = $hwid_parts[1];
    $device->description = $mem_dev->getDescription();
    $device->properties = json_encode($mem_dev->getInitDataValues());
    $full_class_name = get_class($mem_dev);
    die('Не выполнена проверка является ли класс устройством плагина');
} else {
    if ($uid) {
        $device = SmartHome::deviceDatabase()->get($uid);
        if (!$device) {
            $response->returnError(404, 'Устройство не найдено в базе данных');
        }
        $properties = json_decode($device->properties);
        $mem_dev = SmartHome::deviceStorage()->get($device->plugin . ':' . $device->hwid);
        if (!$mem_dev) {
            $mem_dev = SmartHome::deviceFactory()->create($device->plugin, '', $device->class, '{}');
        }
        $class = $device->class;
        if ($mem_dev) {
            $init_data_list = $mem_dev->getInitDataList();
        } else {
            $init_data_list = [];
        }
    } else {
        $device = new FSA\SmartHome\Entity\Device;
        $init_data_list = [];
        $class = '';
    }
}
$response->showHeader("Регистрация оборудования");
if ($hwid) {
    Templates\SmartHome\DeviceInMemory::show($mem_dev);
}
?>
<form method="POST" action="./">
<?php
    if ($uid) {
        Forms::inputHidden('old_uid', $device->uid);
    }
    Forms::inputString('uid', $device->uid, 'Уникальное имя устройства в системе:');
    Forms::inputString('description', $device->description, 'Описание:');
    Forms::inputString('plugin', $device->plugin, 'Плагин устройства*:');
    Forms::inputString('hwid', $device->hwid, 'Уникальный идентификатор устройства*:');
    Forms::inputString('class', $device->class, 'Класс устройства*:');
    foreach ($mem_dev->getInitDataValues() as $param => $value) {
        if (isset($init_data_list[$param])) {
            Forms::inputString('properties[' . $param . ']', isset($value) ? $value : '', $init_data_list[$param]);
        } else {
            Forms::inputHidden('properties[' . $param . ']', isset($value) ? $value : '');
        }
    }
?>
<p>Параметры, отмеченные * не следует изменять для автоматически обнаруженных устройств, т.к. это может повлиять на их доступность.</p>
<?php
    Forms::submitButton($uid ? 'Сохранить изменения' : 'Добавить новое устройство', $uid ? 'update' : 'insert');
    ?>
</form>
<?php
$response->showFooter();
