<?php

require_once '../common.php';
$mem=new Shm();
$yeelight=$mem->getVar(1);
$xiaomi=$mem->getVar(2);
?>
<style>
    table {
        width: 100%;
        border-collapse: collapse;
    }
    
    th,td {
        border: solid 1px black;
        padding: 0.3rem;
    }
</style>
<h1>Xiaomi</h1>
<table>
    <tr>
        <th>ID</th>
        <th>Наименование</th>
        <th>Информация</th>
        <th>Был активен</th>
    </tr>
<?php
foreach ($xiaomi as $dev) {
?>
    <tr>
        <td><?=$dev->getDeviceId()?></td>
        <td><?=$dev->getDeviceName()?></td>
        <td></td>
        <td><?=$dev->getLastUpdate()?></td>
    </tr>
<?php
}
?>
</table>
<h1>Yeelight</h1>
<table>
    <tr>
        <th>ID</th>
        <th>Наименование</th>
        <th>Информация</th>
        <th>Был активен</th>
    </tr>
<?php
foreach ($yeelight as $dev) {
?>
    <tr>
        <td><?=$dev->getDeviceId()?></td>
        <td><?=$dev->getDeviceName()?></td>
        <td></td>
        <td><?=$dev->getLastUpdate()?></td>
    </tr>
<?php
}
?>
</table>