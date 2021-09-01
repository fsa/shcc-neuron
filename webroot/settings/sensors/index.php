<?php

require_once '../../common.php';
Session::grantAccess([]);
httpResponse::setTemplate(new Templates\PageSettings);
httpResponse::showHtmlHeader('Датчики');
?>
<p><a href="edit/" class="btn btn-primary">Создать новый датчик</a></p>
<?php
$meters=new HTML\Table;
$meters->setCaption('Датчики');
$meters->addField('uid', 'Имя');
$meters->addField('value', 'Значение');
$meters->addField('updates', 'Обновлено');
$meters->addField('property_name', 'Величина');
$meters->addField('description', 'Описание');
$meters->addField('device_property', 'Источник данных');
$meters->addButton(new HTML\ButtonLink('Изменить', 'edit/?id=%s'));
$meters->setRowCallback(function ($row) {
    $row->property_name=SmartHome\Sensors::getPropertyName($row->property);
    $state=SmartHome\SensorStorage::get($row->uid);
    if($state) {
        if(is_bool($state->value)) {
            $state->value=$state->value?'да':'нет';
        } else if(is_string($state->value)) {
            if(strlen($state->value)>18) {
                $state->value='<span title="'.htmlspecialchars($state->value).'">'.htmlspecialchars(mb_substr($state->value, 0, 15)).'...</span>';
            } else {
                $state->value=htmlspecialchars($state->value);
            }
        }
        $unit=SmartHome\Sensors::getPropertyUnit($row->property);
        $row->value=$unit?$state->value.' '.$unit:$state->value;
        $row->updates=date('d.m.Y H:i:s', $state->ts);
    } else {
        $row->value='---';
        $row->updates='Нет данных';
    }
});
$meters->showTable(\SmartHome\Sensors::getAll());
httpResponse::showHtmlFooter();