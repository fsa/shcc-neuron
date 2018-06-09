<?php

require 'autoloader.php';
$mem=new Shm();
$var=$mem->getVar(1);
#var_dump($var);
#$var['0x0000000005383a0a']->sendSetPower(true);

$var['0x0000000005438b97']->sendSetPower(true);
sleep(2);
$var['0x0000000005438b97']->sendSetPower(false);
