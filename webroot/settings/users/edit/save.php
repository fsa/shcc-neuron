<?php

use FSA\Neuron\UserDB\UserEntity;

if (!isset($action)) {
    die;
}
$user=UserEntity::getEntity(App::sql(), 'uuid');
$user->inputPostString('login');
$user->inputPostString('password');
$user->inputPostString('name');
$user->inputPostString('email');
$user->inputPostCheckboxArray('scope');
$user->inputPostCheckboxArray('groups');
$user->inputPostCheckbox('disabled');
$uuid=$user->uuid;
$user->upsert();
if ($uuid) {
    App::response()->storeNotification("Пользователь $user->login изменён.");
} else {
    App::response()->storeNotification("Пользователь $user->login создан.");
}
App::response()->redirection('../');