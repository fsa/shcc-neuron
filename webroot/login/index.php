<?php

require_once '../common.php';
$fail2ban=getenv('FAIL2BAN')!==false;
if ($fail2ban and UserDB\User::ipIsBlocked()) {
    httpResponse::showError('Ваш IP заблокирован');
    exit;
}
$login=filter_input(INPUT_POST, 'login');
$password=filter_input(INPUT_POST, 'password');
if ($login===false and $password===false) {
    httpResponse::showLoginForm('/');
    exit;
}
if ($fail2ban and UserDB\User::loginIsBlocked($login)) {
    httpResponse::showError('Пользователь заблокирован');
    exit;
}
$user=UserDB\User::login($login, $password);
if (!$user) {
    if($fail2ban) {
        UserDB\User::addFail($login);
    }
    httpResponse::showError('Неверное имя пользователя или пароль!');
    exit;
}
Session::start($user);
$url=filter_input(INPUT_POST, 'redirect_uri');
httpResponse::redirection(is_null($url)?'/':$url);
