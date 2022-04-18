<?php

use FSA\Neuron\UserDB\UserEntity,
    FSA\Neuron\UserDB\ScopeEntity,
    FSA\Neuron\UserDB\GroupEntity;
use Templates\Forms;

require_once '../../../../vendor/autoload.php';
App::initHtml();
App::session()->grantAccess([]);
$action=filter_input(INPUT_POST, 'action');
if ($action) {
    require 'save.php';
    exit;
}
$user=UserEntity::getEntity(App::sql(), 'uuid', INPUT_GET);
App::response()->showHeader('Редактировать пользователя '.$user->login);
Forms::formHeader('POST', './');
if($user->uuid) {
    Forms::inputHidden('uuid', $user->uuid);
}
Forms::inputString('login', $user->login, 'Логин');
Forms::inputPassword('password', '', 'Пароль (оставьте поле пустым, если его не нужно менять)');
if(password_needs_rehash($user->password_hash, PASSWORD_DEFAULT, ['cost'=>12])) {
?>
<p class="text-danger">Хеш пароля не соответствует текущим требованиям безопасности. Рекомендуется обновить хеш.</p>
<?php
}
Forms::inputString('name', $user->name, 'Имя пользователя');
Forms::inputString('email', $user->email, 'Электронная почта');
?>
<br>
<div class="card">
<div class="card-header">Права доступа:</div>
<?php
foreach (ScopeEntity::getScopes(App::sql()) as $scope=>$title) {
    Forms::inputCheckbox('scope['.$scope.']', $user->memberOfScope($scope), $title);
}
Forms::inputCheckbox('disabled', $user->disabled, 'Пользователь заблокирован');
?>
</div>
<?php
$groups=GroupEntity::getGroups(App::sql());
if(count($groups)) {
?>
<br>
<div class="card">
<div class="card-header">Группы доступа:</div>
<?php
foreach ($groups as $group=>$title) {
    Forms::inputCheckbox('groups['.$group.']', $user->memberOfGroup($group), $title);
}
?>
</div>
<?php
}
?>
<br>
<?php
Forms::submitButton($user->uuid?'Сохранить':'Создать', 'save');
Forms::formFooter();
App::response()->showFooter();
