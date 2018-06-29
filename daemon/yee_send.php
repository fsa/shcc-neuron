<?php

require_once 'autoloader.php';
$mem=new \SmartHome\DeviceMemoryStorage;

$devices=$mem->getModuleDevices('yeelight');
$yeelight=$devices['bslamp1_0x0000000005438b97'];
var_dump($yeelight);
$c=1;
echo $c++.": ".$yeelight->sendToggle();
#echo $c++.": ".$yeelight->sendSetPower(true,2000);
#sleep(3);
#echo $c++.": ".$yeelight->sendSetRGB("00FF00",2000);
#sleep(3);
#echo $c++.": ".$yeelight->sendSetRGB("FFFFFF",2000);
#sleep(3);
#sleep(3);
#echo $c++.": ".$yeelight->sendSetBright(100);
sleep(3);
#echo $c++.": ".$yeelight->sendSetPower(false,2000);
echo $c++.": ".$yeelight->sendToggle();
var_dump($yeelight->getResponse());
$yeelight->disconnect();
