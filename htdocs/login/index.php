<?php

require_once '../common.php';
if(Auth\Fail2Ban::ipIsBlocked()) {
    httpResponse::showError('Ваш IP заблокирован');
}
$login=filter_input(INPUT_POST,'login');
$password=filter_input(INPUT_POST,'password');
if (!$login or!$password) {
    httpResponse::showLoginForm('/');
    exit;
}
if(Auth\Fail2Ban::loginIsBlocked($login)) {
    httpResponse::showError('Пользователь заблокирован');
}
$user=Auth\User::authenticate($login,$password);
if(is_null($user)) {
    Auth\Fail2Ban::addFail($login);
    httpResponse::showError('Неверное имя пользователя или пароль!');
}
Auth\Session::login($user);
$url=filter_input(INPUT_POST,'redirect_uri');
httpResponse::redirection(is_null($url)?'/':$url);
