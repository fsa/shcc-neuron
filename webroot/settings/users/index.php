<?php

require_once '../../common.php';
Auth\Session::grantAccess([]);
httpResponse::setTemplate(new Templates\PageSettings);
httpResponse::showHtmlHeader('Пользователи системы');
?>
<p><a href="edit/" class="btn btn-primary">Создать нового пользователя</a></p>
<?php
$users=new HTML\Table;
$users->setCaption('Список пользователей');
$users->addField('login', 'Логин');
$users->addField('name', 'Имя пользователя');
$users->addField('email', 'Эл. почта');
$users->addField('scope', 'Группы доступа');
$users->addField('registered', 'Дата регистрации');
$users->addField('updated', 'Дата изменения');
$users->addButton(new HTML\ButtonLink('Редактировать', 'edit/?id=%s'));
$users->setRowStyleField('disabled');
$users->setRowCallback(function ($row) {
    $row->registered=is_null($row->registered)?'':date('d.m.Y H:i:s', strtotime($row->registered));
    $row->updated=is_null($row->updated)?'':date('d.m.Y H:i:s', strtotime($row->updated));
    $row->disabled=$row->disabled=='t'?'bg-warning':null;
    $row->scope=is_null($row->scope)?'':join(', ', json_decode($row->scope));
});
$users->showTable(Auth\Admin::getUsersList());
httpResponse::showHtmlFooter();
