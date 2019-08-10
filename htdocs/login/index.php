<?php

require_once '../common.php';
$login=filter_input(INPUT_POST,'login');
$password=filter_input(INPUT_POST,'password');
if (!$login or!$password) {
    HTML::showLoginForm();
    exit;
}
$user=Auth\User::authenticate($login,$password);
Auth\Internal::login($user);
HTML::redirect('../');
