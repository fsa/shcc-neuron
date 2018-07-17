<?php

require_once '../common.php';
$user=new User;
$user->id=1;
$user->login='admin';
$user->name='Админ';
$user->email='admin@localhost';
$user->groups=['admin'];
Auth::login($user);
HTML::redirect('../');