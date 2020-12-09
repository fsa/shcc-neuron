<?php
/**
 * SHCC 0.7.0-dev
 * 2020-11-29
 */
require_once '../../common.php';
Auth\Session::grantAccess([]);
httpResponse::showHtmlHeader('Датчики');
httpResponse::showNavPills('../%s/', require '../sections.php', 'meters');
?>
<hr>
<?php
$mem=new \SmartHome\MemoryStorage;
$meters=new HTML\Table;
$meters->setCaption('Датчики');
$meters->addField('uid', 'Имя');
$meters->addField('value', 'Значение');
$meters->addField('updates', 'Обновлено');
$meters->addField('unit_name', 'Величина');
$meters->addField('description', 'Описание');
$meters->addField('device_property', 'Источник данных');
#$meters->addButton(new HTML\ButtonLink('Изменить', 'edit/?id=%s'));
$meters->setRowCallback(function ($row) use ($mem) {
    $row->unit_name=\SmartHome\Meters::getUnitName($row->unit);
    $state=$mem->getSensor($row->uid);
    if($state) {
        $row->value=$state->value.' '.\SmartHome\Meters::getUnit($row->unit);
        $row->updates=date('d.m.Y H:i:s', $state->ts);
    } else {
        $row->value='---';
        $row->updates='Нет данных';
    }
});
$meters->showTable(\SmartHome\Meters::getMeters());
httpResponse::showHtmlFooter();