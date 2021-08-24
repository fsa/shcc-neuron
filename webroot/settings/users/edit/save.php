<?php

if (!isset($action)) {
    die;
}
$user=UserDB\UserEntity::getEntity('uuid');
$user->inputPostString('login');
$user->inputPostString('password');
$user->inputPostString('name');
$user->inputPostString('email');
$user->inputPostChecboxArray('scope');
$user->inputPostChecboxArray('groups');
$user->inputPostCheckbox('disabled');
$uuid=$user->uuid;
$user->upsert();
if ($uuid) {
    httpResponse::storeNotification("Пользователь $user->login изменён.");
} else {
    httpResponse::storeNotification("Пользователь $user->login создан.");
}
httpResponse::redirection('../');