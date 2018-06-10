<?php

require 'autoloader.php';

const XIAOMI_SHM_CODE=2;

$mem=new Shm();
$devices=$mem->getVar(XIAOMI_SHM_CODE);
if($devices===false) {
    $devices=[];
}
$xiaomi=new Xiaomi\SocketServer();
$xiaomi->run();
do {
    $pkt=$xiaomi->getPacket();
    $sid=$pkt->getSid();
    if (is_null($sid)) {
        continue;
    }
    if (!isset($devices[$sid])) {
        $device=$pkt->getDeviceObject();
        if (is_null($device)) {
            $filename='xiaomi/'.$pkt->getSid().'.log';
            file_put_contents($filename,date('c').PHP_EOL.print_r($pkt,true),FILE_APPEND);
        } else {
            $devices[$sid]=$device;
        }
    }
    if (isset($devices[$sid])) {
        $devices[$sid]->update($pkt);
        $mem->setVar(XIAOMI_SHM_CODE,$devices);
        $actions=$devices[$sid]->getActions();
        if(!is_null($actions)) {
            #file_put_contents('http://127.0.0.1:81/action/?module=xiaomi&sid='.$sid,$actions);
            echo date('c').' '.$sid.'=>'.$actions.PHP_EOL;
        }
    }
} while (1);
