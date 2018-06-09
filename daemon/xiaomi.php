<?php

require 'autoloader.php';

$devices=[];
$mem=new Shm();
$xiaomi=new Xiaomi\SocketServer();
$xiaomi->run();
do {
    $pkt=$xiaomi->getPacket();
    $sid=$pkt->getSid();
    if(is_null($sid)) {
        continue;
    }
    if (!isset($devices[$sid])) {
        switch ($pkt->getModel()) {
            case "gateway":
                $devices[$sid]=new Xiaomi\Devices\XiaomiGateway;
                break;
            case "weather.v1":
            case "senshor_ht":
                $devices[$sid]=new Xiaomi\Devices\TemperatureHumiditySensor;
                break;
            case "motion":
                $devices[$sid]=new Xiaomi\Devices\MotionSensor;
                break;
#            case "magnet":
#                $devices[$sid]=new Xiaomi\Devices\MagnetSensor;
#                break;
            default:
                $filename='xiaomi/'.$pkt->getSid().'.log';
                file_put_contents($filename,date('c').PHP_EOL.print_r($pkt,true),FILE_APPEND);
        }
    }
    if(isset($devices[$sid])) {
        $devices[$sid]->update($pkt);
        $mem->setVar(2,$devices);
    }
} while (1);
