<?php

require_once '../../vendor/autoload.php';
App::initHtml();
$login = filter_input(INPUT_POST, 'login');
$password = filter_input(INPUT_POST, 'password');
if (!$login or !$password) {
    if (App::session()->memberOf()) {
        if (filter_input(INPUT_GET, 'action')=='logout') {
            App::logout();
            App::response()->redirection('../');
        }
        App::response()->returnError(200, 'Вы уже в залогинены');
    }
    App::response()->showLoginForm('/');
    exit;
}
App::login($login, $password);
$url = filter_input(INPUT_POST, 'redirect_uri');
App::response()->redirection(is_null($url) ? '/' : $url);
