<?php

require_once '../common.php';
if(Auth\Fail2Ban::ipIsBlocked()) {
    throw new AppException('Ваш IP заблокирован');
}
$login=filter_input(INPUT_POST,'login');
$password=filter_input(INPUT_POST,'password');
if (!$login or!$password) {
    HTML::showLoginForm('/');
    exit;
}
if(Auth\Fail2Ban::loginIsBlocked($login)) {
    throw new AppException('Пользователь заблокирован');
}
$user=Auth\User::authenticate($login,$password);
if(is_null($user)) {
    Auth\Fail2Ban::addFail($login);
    HTML::showException('Неверное имя пользователя или пароль!');
    exit;
}
Auth\Internal::login($user);
$url=filter_input(INPUT_POST,'redirect_uri');
httpResponse::redirect(is_null($url)?'/':$url);
