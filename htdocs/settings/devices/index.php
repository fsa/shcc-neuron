<?php

require_once '../../common.php';
Auth\Internal::grantAccess(['admin']);
HTML::showPageHeader('Устройтва');
?>
<p><a href="../">Вернуться в настройки</a></p>
<hr>
<p><a href="memory/">Просмотр и добавление обнаруженных устройств</a></p>
<p><a href="edit/">Добавить новое устройство вручную</a></p>
<?php
$devices=new HTML\Table;
$devices->addField('unique_name','Имя');
$devices->addField('description','Описание');
$devices->addField('classname','Класс');
$devices->addField('place','Место установки');
$devices->addButton(new HTML\ButtonLink('Датчики','sensors/?id=%s'));
$devices->addButton(new HTML\ButtonLink('Изменить','edit/?id=%s'));
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