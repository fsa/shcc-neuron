<?php

if (!isset($module) or !isset($id)) {
    die;
}
$sh=new SmartHome\DeviceMemoryStorage;
$memdevices=$sh->getModuleDevices($module);
if(!isset($memdevices[$id])) {
    throw new AppException('Что-то пошло не так. Не найдено устройство в памяти.');
}
$device=$memdevices[$id];
HTML::showPageHeader("Регистрация оборудования модуля '$module'");
?>
<p>Перед использованием устройства его необходимо его зарегистрировать в системе.</p>
<form method="POST" action="register/">
<input type="hidden" name="module" value="<?=$module?>">
<input type="hidden" name="uid" value="<?=$id?>">
<input type="hidden" name="classname" value="<?=get_class($device)?>">
<table>
    <tr>
        <th>Параметр</th>
        <th>Значение</th>
    </tr>
    <tr>
        <td>Модуль</td>
        <td><?=$device->getModuleName()?></td>
    </tr>
    <tr>
        <td>ID устройства</td>
        <td><?=$device->getDeviceId()?></td>
    </tr>
    <tr>
        <td>Состояние</td>
        <td><?=$device->getDeviceStatus()?></td>
    </tr>
<?php
if($device instanceof \SmartHome\SensorsInterface) {
?>
    <tr>
        <td>Аналоговые датчики</td>
        <td><?=join(', ',$device->getDeviceMeters())?></td>
    </tr>
    <tr>
        <td>Цифровые датчики</td>
        <td><?=join(', ',$device->getDeviceIndicators())?></td>
    </tr>
<?php
}
?>
    <tr>
        <td>Уникальный ID в системе</td>
        <td><input type="text" name="unique_name" value="<?=$module.'_'.$id?>"></td>
    </tr>
    <tr>
        <td>Наименование</td>
        <td><input type="text" name="name" value="<?=$device->getDeviceName()?>"></td>
    </tr>
<?php
$values=$device->getInitDataValues();
foreach ($device->getInitDataList() as $param=>$name) {
?>
    <tr>
        <td><?=$name?></td>
        <td><input type="text" name="init[<?=$param?>]" value="<?=$values[$param]?>"></td>
    </tr>
<?php
}
?>
    <tr>
        <td>Расположение</td>
        <td><select name="place_id">
                <option value="">Не установлен</option>
            </select></td>
    </tr>
</table>
<input type="submit" value="Зарегистрировать">
</form>
<?php
HTML::showPageFooter();