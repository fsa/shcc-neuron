<?php

require_once '../../common.php';
Session::grantAccess([]);
httpResponse::setTemplate(new Templates\PageSettings);
httpResponse::showHtmlHeader('Пользователи');
?>
<p><a href="edit/" class="btn btn-secondary">Создать нового пользователя</a></p>
<?php
$users=new HTML\DBQuery(new Templates\Settings\Users);
$users->setCaption('Список пользователей');
$users->show(UserDB\User::stmtGetAll());
httpResponse::showHtmlFooter();
