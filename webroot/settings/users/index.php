<?php

use FSA\Neuron\HTML\DBQuery,
    FSA\Neuron\UserDB\UserEntity;

require_once '../../../vendor/autoload.php';
App::initHtml();
App::session()->grantAccess([]);
App::response()->showHeader('Пользователи');
?>
<p><a href="edit/" class="btn btn-secondary">Создать нового пользователя</a></p>
<?php
$users=new DBQuery(new Templates\Settings\Users);
$users->setCaption('Список пользователей');
$users->show(UserEntity::stmtGetAll(App::sql()));
App::response()->showFooter();
