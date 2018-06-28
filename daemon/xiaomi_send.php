<?php

#$xiaomiGateway->setKey('td1ufnw4y0js7zfs');
# f0b4299a72d0 - geteway
# 158d0001f50bba - weather.v1
# 158d00010e3a1a - senshor_ht
# 158d00015a89a8 - motion
# 158d0001537514 - magnet
require_once 'autoloader.php';
$mem=new \SmartHome\DeviceMemoryStorage;

$devices=$mem->getModuleDevices('xiaomi');
$message=$devices['f0b4299a72d0']->prepareCommand(['rgb'=>hexdec('64FFFFFF')]);
$devices['f0b4299a72d0']->sendMessage($message);
sleep(1);
$message=$devices['f0b4299a72d0']->prepareCommand(['rgb'=>'FFFFFF']);
$devices['f0b4299a72d0']->sendMessage($message);

# add_channels {"chs":[{"id":555555,"url":"https://rmgradio.gcdn.co/hit_m.aac","type":0}]}
