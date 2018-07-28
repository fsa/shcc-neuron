<?php

require_once '../common.php';
$login=filter_input(INPUT_POST,'login');
$password=filter_input(INPUT_POST,'password');
if (!$login or!$password) {
    HTML::showLoginForm();
    exit;
}
$user=new Auth\UserEntity();
switch ($login) {
    case "admin":
        $user->id=1;
        $user->login='admin';
        $user->name='Админ';
        $user->email='admin@localhost';
        $user->groups='["admin"]';
        break;
    case "user":
        $user->id=2;
        $user->login='user';
        $user->name='Пользователь';
        $user->email='user@localhost';
        $user->groups='[]';
        break;
    default:
        throw new AppException('Неверное имя пользователя или пароль');
}
Auth::login($user);
HTML::redirect('../');
