<?php

require_once '../common.php';
$login=filter_input(INPUT_POST,'login');
$password=filter_input(INPUT_POST,'password');
if (!$login or!$password) {
    HTML::showLoginForm('/');
    exit;
}
$user=Auth\User::authenticate($login,$password);
if(is_null($user)) {
    HTML::showException('Неверное имя пользователя или пароль!');
    exit;
}
Auth\Internal::login($user);
$url=filter_input(INPUT_POST,'redirect_uri');
HTML::redirect(is_null($url)?'/':$url);
