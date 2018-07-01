<?php

namespace Templates\SmartHome;

class DeviceInMemory {
    public static function show($device) {
?>
<table>
    <caption>Данные устройства в памяти</caption>
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
</table>
<?php
    }
}