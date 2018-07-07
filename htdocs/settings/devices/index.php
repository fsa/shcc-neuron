<?php

require_once '../../common.php';
HTML::showPageHeader('Устройтва');
?>
<p><a href="memory/">Устройства в памяти</a></p>
<p><a href="edit/">Добавить новое устройство вручную</a></p>
<?php
$devices=new Table;
$devices->addField('unique_name','Имя');
$devices->addField('name','Описание');
$devices->addField('classname','Класс');
$devices->addField('place','Место установки');
$devices->addButton('Изменить','edit/?id=%s');
$devices->setRowStyleField('style');
$devices->showTable(\SmartHome\Devices::getDevicesStmt());
?>
<table>
    <tr class="table-danger">
        <td>Отключенные устройства</td>
    </tr>
</table>
<?php
HTML::showPageFooter();