<?php

if (!isset($action)) {
    die;
}
$user=Auth\UserEntity::getEntity('id');
$user->inputPostString('login');
$user->inputPostString('password');
$user->inputPostString('name');
$user->inputPostString('email');
$user->inputPostChecboxArray('scope');
$user->inputPostCheckbox('disabled');
$id=$user->id;
$user->upsert();
if ($id) {
    httpResponse::storeNotification("Пользователь $user->login изменён.");
} else {
    httpResponse::storeNotification("Пользователь $user->login создан.");
}
httpResponse::redirection('../');