<?php

use FSA\Neuron\HTML\Table,
    FSA\Neuron\HTML\ButtonLink;

require_once '../../../vendor/autoload.php';
App::initHtml(Templates\PageSettings::class);
App::session()->grantAccess([]);
App::response()->showHeader('Устройства');
?>
<p><a href="memory/" class="btn btn-primary">Добавить новое устройство</a></p>
<?php
$devices = new Table;
$devices->setCaption('Устройства умного дома');
$devices->addField('uid', 'Имя');
$devices->addField('hwid', 'Идентификатор устройства');
$devices->addField('description', 'Описание');
$devices->addField('classname', 'Класс');
$devices->addField('info', 'Информация об устройстве');
$devices->addField('events', 'События');
$devices->addField('updated', 'Было активно');
$devices->addButton(new ButtonLink('Изменить', 'edit/?uid=%s', 'uid'));
$devices->setRowCallback(function ($row) {
    $entity = json_decode($row->entity);
    $row->classname = $entity->classname;
    $dev = SmartHome\Devices::get($row->uid);
    if (is_null($dev)) {
        $row->info = '';
        $row->updated = '';
    } else {
        try {
            $row->info = (string) $dev;
        } catch (Exception $ex) {
            $row->info = 'Программная ошибка: ' . $ex->getMessage();
        }
        try {
            $updated = $dev->getLastUpdate();
            $row->updated = $updated ? date('d.m.Y H:i:s', $updated) : 'Нет данных';
        } catch (Exception $ex) {
            $row->updated = 'Ошибка: ' . $ex->getMessage();
        }
    }
    $row->events = join(', ', $dev->getEventsList());
});
$devices->showTable(\SmartHome\Devices::getDevicesStmt());
?>
<table>
    <tr class="table-danger">
        <td>Отключенные устройства</td>
    </tr>
</table>
<?php
App::response()->showFooter();
