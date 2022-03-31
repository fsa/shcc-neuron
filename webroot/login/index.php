<?php

require_once '../common.php';

use FSA\Neuron\HttpResponse,
    FSA\Neuron\Session,
    FSA\Neuron\User;

$login = filter_input(INPUT_POST, 'login');
$password = filter_input(INPUT_POST, 'password');
if (!$login or !$password) {
    if (Session::memberOf()) {
        if (filter_input(INPUT_GET, 'action')=='logout') {
            Session::drop();
            HttpResponse::redirection('../');
        }
        #TODO: Пользователь уже в системе, предложить выйти
    }
    HttpResponse::showLoginForm('/');
    exit;
}
$user = User::login($login, $password);
if (!$user) {
    HttpResponse::showError('Неверное имя пользователя или пароль.');
    exit;
}
Session::start($user);
$url = filter_input(INPUT_POST, 'redirect_uri');
HttpResponse::redirection(is_null($url) ? '/' : $url);
