<?php

require_once '../common.php';
$login=filter_input(INPUT_POST,'login');
$password=filter_input(INPUT_POST,'password');
if($login and $password) {
    require 'login.php';
    die;
}
HTML::showLoginForm();