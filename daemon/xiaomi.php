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
                $devices[$sid]=new Xiaomi\Devices\AqaraWeatherSensor;
                break;
            case "sensor_ht":
                $devices[$sid]=new Xiaomi\Devices\XiaomiHTSensor;
                break;
            case "motion":
                $devices[$sid]=new Xiaomi\Devices\MotionSensor;
                break;
            case "magnet":
                $devices[$sid]=new Xiaomi\Devices\MagnetSensor;
                break;
#            case "switch":
#                $devices[$sid]=new Xiaomi\Devices\Switch;
#                break;
#            case "sensor_wleak.aq1":
#                $devices[$sid]=new Xiaomi\Devices\AqaraWleakSensor;
#                break;
#
#            case "sensor_switch.aq2":
#                $devices[$sid]=new Xiaomi\Devices\AqaraSwitchSensor2;
#                break;
#            case "sensor_switch.aq3":
#                $devices[$sid]=new Xiaomi\Devices\AqaraSwitchSensor3;
#                break;
#            case "86sw1":
#                $devices[$sid]=new Xiaomi\Devices\AqaraWleakSensor;
#                break;
#            case "86sw2":
#                $devices[$sid]=new Xiaomi\Devices\AqaraWleakSensor;
#                break;
#            case "sensor_wleak.aq1":
#                $devices[$sid]=new Xiaomi\Devices\AqaraWleakSensor;
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
