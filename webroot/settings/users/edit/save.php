<?php
use FSA\Neuron\HttpResponse,
    FSA\Neuron\UserDB\UserEntity;
if (!isset($action)) {
    die;
}
$user=UserEntity::getEntity('uuid');
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
    HttpResponse::storeNotification("Пользователь $user->login изменён.");
} else {
    HttpResponse::storeNotification("Пользователь $user->login создан.");
}
HttpResponse::redirection('../');