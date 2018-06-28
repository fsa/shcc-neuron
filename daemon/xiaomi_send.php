<?php

#$xiaomiGateway->setKey('td1ufnw4y0js7zfs');
# f0b4299a72d0 - geteway
# 158d0001f50bba - weather.v1
# 158d00010e3a1a - senshor_ht
# 158d00015a89a8 - motion
# 158d0001537514 - magnet
require_once 'autoloader.php';
$mem=new MemoryStorage();

$devices=$mem->getArray('xiaomi');
$devices['f0b4299a72d0']->setKey(\Settings::get('xiaomi')->devices_keys->f0b4299a72d0);
$message=$devices['f0b4299a72d0']->prepareCommand(['set_led'=>'on']);
$devices['f0b4299a72d0']->sendMessage($message);
sleep(3);
$message=$devices['f0b4299a72d0']->prepareCommand(['set_led'=>'off']);
$devices['f0b4299a72d0']->sendMessage($message);

# add_channels {"chs":[{"id":555555,"url":"https://rmgradio.gcdn.co/hit_m.aac","type":0}]}
