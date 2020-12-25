<?php
/**
 * SHCC 0.7.0
 * 2020-11-29
 */
require_once '../../common.php';
Auth\Session::grantAccess([]);
httpResponse::setTemplate(\Templates\PageSettings::class);
httpResponse::showHtmlHeader('Устройтва');
?>
<p><a href="memory/" class="btn btn-primary">Добавить новое устройство</a></p>
<?php
$devices=new HTML\Table;
$devices->setCaption('Устройства умного дома');
$devices->addField('uid', 'Имя');
$devices->addField('hwid', 'Идентификатор устройства');
$devices->addField('description', 'Описание');
$devices->addField('classname', 'Класс');
$devices->addField('info', 'Информация об устройстве');
$devices->addField('events', 'События');
$devices->addField('updated', 'Было активно');
$devices->addButton(new HTML\ButtonLink('Изменить', 'edit/?uid=%s', 'uid'));
$devices->setRowCallback(function ($row) {
    $entity=json_decode($row->entity);
    $row->classname=$entity->classname;
    $dev=SmartHome\Devices::get($row->uid);
    if (is_null($dev)) {
        $row->info='';
        $row->updated='';
    } else {
        try {
            $row->info=(string) $dev;
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
    $row->events=join(', ', $dev->getEventsList());
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