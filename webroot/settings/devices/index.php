<?php

use FSA\Neuron\HTML\Table,
    FSA\Neuron\HTML\ButtonLink;

require_once '../../../vendor/autoload.php';
$response = App::initHtml(Templates\PageSettings::class);
App::session()->grantAccess([]);
$response->showHeader('Устройства');
?>
<p><a href="memory/" class="btn btn-primary">Добавить новое устройство</a></p>
<?php
$devices = new Table;
$devices->setCaption('Устройства умного дома');
$devices->addField('uid', 'Имя');
$devices->addField('description', 'Описание');
$devices->addField('plugin', 'Плагин');
$devices->addField('hwid', 'Идентификатор устройства');
$devices->addField('class', 'Описание устройства');
$devices->addField('info', 'Состояние устройства');
$devices->addField('updated', 'Было активно');
$devices->addButton(new ButtonLink('Изменить', 'edit/?uid=%s', 'uid'));
$device_storage = SmartHome::deviceStorage();
$devices->setRowCallback(function ($row) use ($device_storage) {
    $dev = $device_storage->get($row->plugin . ':' . $row->hwid);
    if (is_null($dev)) {
        $row->info = '';
        $row->updated = '';
        $row->events = '';
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
        $row->class = 'Тип устройства: '. $row->class. '<br>События: ' . join(', ', $dev->getEventsList());
    }
});
$devices->showTable(SmartHome::deviceDatabase()->getAll());
?>
<table>
    <tr class="table-danger">
        <td>Отключенные устройства</td>
    </tr>
</table>
<?php
$response->showFooter();
