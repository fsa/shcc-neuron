<?php
require_once '../../common.php';
Auth\Session::grantAccess([]);
httpResponse::showHtmlHeader('Устройтва');
httpResponse::showNavPills('../%s/', require '../sections.php', 'devices');
?>
<hr>
<p><a href="memory/">Добавить новое устройство</a></p>
<?php
$devices=new HTML\Table;
$devices->setCaption('Устройства умного дома');
$devices->addField('unique_name', 'Имя');
$devices->addField('uid', 'Идентификатор устройства');
$devices->addField('description', 'Описание');
$devices->addField('classname', 'Класс');
$devices->addField('place', 'Место установки');
$devices->addField('info', 'Информация об устройстве');
$devices->addField('updated', 'Было активено');
$devices->addButton(new HTML\ButtonLink('Датчики', 'sensors/?id=%s'));
$devices->addButton(new HTML\ButtonLink('Изменить', 'edit/?id=%s'));
$devices->setRowStyleField('style');
$devices->setRowCallback(function ($row) {
    $dev=SmartHome\Devices::get($row->unique_name);
    if(is_null($dev)) {
        $row->info='';
        $row->updated='';
    } else {
        $row->info=$dev->getDeviceStatus();
        $updated=$dev->getLastUpdate();
        $row->updated=$updated?date('d.m.Y H:i:s', $updated):'Нет данных';
    }
});
$devices->showTable(\SmartHome\Devices::getDevicesStmt());
?>
<table>
    <tr class="table-danger">
        <td>Отключенные устройства</td>
    </tr>
</table>
<?php
httpResponse::showHtmlFooter();
