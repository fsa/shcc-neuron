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
$devices->addField('uid', 'Имя');
$devices->addField('hwid', 'Идентификатор устройства');
$devices->addField('description', 'Описание');
$devices->addField('classname', 'Класс');
$devices->addField('place_name', 'Место установки');
$devices->addField('info', 'Информация об устройстве');
$devices->addField('updated', 'Было активно');
$devices->addButton(new HTML\ButtonLink('Датчики', 'sensors/?id=%s', 'hwid'));
$devices->addButton(new HTML\ButtonLink('Изменить', 'edit/?id=%s', 'hwid'));
$devices->setRowCallback(function ($row) {
    $entity=json_decode($row->entity);
    $row->classname=$entity->classname;
    $dev=SmartHome\Devices::get($row->uid);
    if (is_null($dev)) {
        $row->info='';
        $row->updated='';
    } else {
        try {
            $row->info=$dev->getStateString();
        } catch (Exception $ex) {
            $row->info='Программная ошибка: '.$ex->getMessage();
        }
        try {
            $updated=$dev->getLastUpdate();
            $row->updated=$updated?date('d.m.Y H:i:s', $updated):'Нет данных';
        } catch (Exception $ex) {
            $row->updated='Ошибка: '.$ex->getMessage();
        }
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
