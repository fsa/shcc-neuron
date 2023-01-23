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
    $mem_dev = App::deviceStorage()->get($hwid);
    if (is_null($mem_dev)) {
        $response->returnError(404, 'Информация об устройстве в памяти не найдена.');
    }
    $hwid_parts = explode(':', $hwid, 2);
    if (sizeof($hwid_parts) != 2) {
        $response->returnError(400, 'Неверный аппаратный идентификатор устройства');
    }
    $init_data_list = $mem_dev->getInitDataList();
    $init_data_values = $mem_dev->getInitDataValues();
    $device = new FSA\SmartHome\Entity\Device;
    $device->uid = $hwid;
    $device->plugin = $hwid_parts[0];
    $device->hwid = $hwid_parts[1];
    $device->description = $mem_dev->getDescription();
    $device->properties = json_encode($init_data_values);
    $full_class_name = explode('\\', get_class($mem_dev));
    $device->class = array_pop($full_class_name);
    if (array_pop($full_class_name) != 'Devices') {
        $response->returnError(500, 'Класс PHP используемый устройством, не соответствует требованиям системы');
    }
    $plugins = App::plugins()->get();
    if (!isset($plugins[$device->plugin])) {
        $response->returnError(500, 'Не найден плагин, которому принадлежит устройство.');
    }
    if (join('\\', $full_class_name) . '\\' != $plugins[$device->plugin]) {
        $response->returnError(500, 'Класс PHP для устройства не принадлежит плагину.');
    }
} else {
    if ($uid) {
        $device = App::deviceDatabase()->get($uid);
        if (!$device) {
            $response->returnError(404, 'Устройство не найдено в базе данных');
        }
        $mem_dev = App::deviceStorage()->get($device->plugin . ':' . $device->hwid);
        if (!$mem_dev) {
            $mem_dev = App::deviceFactory()->create($device->plugin, '', $device->class, $device->properties);
        }
        $class = $device->class;
        if ($mem_dev) {
            $init_data_list = $mem_dev->getInitDataList();
            $init_data_values = $mem_dev->getInitDataValues();
        } else {
            $init_data_list = [];
            $init_data_values = [];
        }
    } else {
        $device = new FSA\SmartHome\Entity\Device;
        $init_data_list = [];
        $init_data_values = [];
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
    Forms::inputString('class', $device->class, 'Тип устройства:');
    foreach ($init_data_values as $param => $value) {
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
