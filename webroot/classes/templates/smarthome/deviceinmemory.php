<?php

/**
 * SHCC 0.7.0
 * 2020-12-25
 */

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
        <td>ID устройства</td>
        <td><?=$device->getHwid()?></td>
    </tr>
    <tr>
        <td>Состояние</td>
        <td><?=(string)$device?></td>
    </tr>
</table>
<?php
    }
}