<?php

namespace Templates\SmartHome;

class DeviceInMemory {
    public static function show($device) {
?>
<table class="table table-striped table-bordered">
    <caption style="caption-side: top;">Данные устройства в памяти</caption>
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
        <td><?=$device->getId()?></td>
    </tr>
    <tr>
        <td>Состояние</td>
        <td><?=$device->getStateString()?></td>
    </tr>
<?php
if($device instanceof \SmartHome\SensorsInterface) {
?>
    <tr>
        <td>Аналоговые датчики</td>
        <td><?=join('<br>',$device->getDeviceMeters())?></td>
    </tr>
    <tr>
        <td>Цифровые датчики</td>
        <td><?=join('<br>',$device->getDeviceIndicators())?></td>
    </tr>
<?php
}
if($device instanceof \SmartHome\DeviceActionInterface) {
?>
    <tr>
        <td>События от устройства</td>
        <td><?=join('<br>',$device->getDeviceActions())?></td>
    </tr>
<?php
}
?>
</table>
<?php
    }
}