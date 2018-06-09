<?php

require 'autoloader.php';
$f=shm_attach(1);
$var=shm_get_var($f,1);
var_dump($var);

$var['0x0000000005438b97']->sendSetPower(true);
sleep(2);
$var['0x0000000005438b97']->sendSetPower(false);
