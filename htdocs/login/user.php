<?php

require_once '../common.php';
$user=new User;
$user->id=1;
$user->login='user';
$user->name='Пользователь';
$user->email='user@localhost';
$user->groups=[];
Auth::login($user);
HTML::redirect('../');