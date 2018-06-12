<?php

require 'autoloader.php';

$mem=new MemoryStorage();
$devices=$mem->getArray('xiaomi');
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
        $mem->setVar('xiaomi',$devices);
        $actions=$devices[$sid]->getActions();
        if (!is_null($actions)) {
            $data=['module'=>'xiaomi','uid'=>$sid,'data'=>$actions];
            file_get_contents(\Settings::get('url').'/action/?'.http_build_query($data));
            echo date('c').' '.$sid.'=>'.$actions.PHP_EOL;
        }
    }
} while (1);
