<?php

use FSA\Neuron\HTML\Table,
    FSA\Neuron\HTML\ButtonLink;
use FSA\SmartHome\Sensor;

require_once '../../../vendor/autoload.php';
App::initHtml(Templates\PageSettings::class);
App::session()->grantAccess([]);
App::response()->showHeader('Датчики');
?>
<p><a href="edit/" class="btn btn-primary">Создать новый датчик</a></p>
<?php
$meters = new Table;
$meters->setCaption('Датчики');
$meters->addField('uid', 'Имя');
$meters->addField('value', 'Значение');
$meters->addField('updates', 'Обновлено');
$meters->addField('property_name', 'Величина');
$meters->addField('description', 'Описание');
$meters->addField('device_property', 'Источник данных');
$meters->addButton(new ButtonLink('Изменить', 'edit/?id=%s'));
$meters->setRowCallback(function ($row) {
    $row->property_name = Sensor::getPropertyName($row->property);
    $state = SmartHome::sensorStorage()->get($row->uid);
    if ($state) {
        if (is_bool($state->value)) {
            $state->value = $state->value ? 'да' : 'нет';
        } else if (is_string($state->value)) {
            if (strlen($state->value) > 18) {
                $state->value = '<span title="' . htmlspecialchars($state->value) . '">' . htmlspecialchars(mb_substr($state->value, 0, 15)) . '...</span>';
            } else {
                $state->value = htmlspecialchars($state->value);
            }
        }
        $unit = Sensor::getPropertyUnit($row->property);
        $row->value = $unit ? $state->value . ' ' . $unit : $state->value;
        $row->updates = date('d.m.Y H:i:s', $state->ts);
    } else {
        $row->value = '---';
        $row->updates = 'Нет данных';
    }
});
$meters->showTable(SmartHome::sensorDatabase()->getAll());
App::response()->showFooter();
