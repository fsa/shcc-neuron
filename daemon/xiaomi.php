<?php

require 'autoloader.php';

$xiaomiGateway=new \Xiaomi\Devices\XiaomiGateway();
$xiaomiGateway->setKey('td1ufnw4y0js7zfs');
$aquaraTemper=new \Xiaomi\Devices\TemperatureHumiditySensor();
$xiaomiTemper=new \Xiaomi\Devices\TemperatureHumiditySensor();
$xiaomiMotion=new Xiaomi\Devices\MotionSensor();

$xiaomi=new Xiaomi\SocketServer();
$xiaomi->run();
do {
    $pkt=$xiaomi->getPacket();
    switch ($pkt->getSid()) {
        case "f0b4299a72d0":
            $xiaomiGateway->update($pkt);
            var_dump($xiaomiGateway);
            #$message=$xiaomiGateway->prepareCommand(['rgb'=>hexdec('64FFFFFF')]);
            #$message=$xiaomiGateway->prepareCommand(['rgb'=>0]);
            #$xiaomi->sendMessage($message,'172.17.23.10:9898');
            break;
        case "158d0001f50bba":
            $aquaraTemper->update($pkt);
            var_dump($aquaraTemper);
            break;
        case "158d00010e3a1a":
            $xiaomiTemper->update($pkt);
            var_dump($xiaomiTemper);
            break;
        case "158d00015a89a8":
            $xiaomiMotion->update($pkt);
            var_dump($xiaomiMotion);
            break;
        default:
        #    var_dump($pkt->getData());
            $filename='log/'.$pkt->getSid().'.log';
            file_put_contents($filename,date('c').PHP_EOL.print_r($pkt,true),FILE_APPEND);
    }
} while (1);
