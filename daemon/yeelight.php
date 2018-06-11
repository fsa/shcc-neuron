<?php

require 'autoloader.php';

const YEELIGHT_SHM_CODE=1;

$mem=new Shm();
$bulbs=$mem->getVar(YEELIGHT_SHM_CODE);
if ($bulbs===false) {
    $bulbs=[];
}
$yeelight=new Yeelight\SocketServer();
$yeelight->run();
$yeelight->sendDiscover();
$shm=new Shm();
do {
    $pkt=$yeelight->getPacket();
    $p=$pkt->getParams();
    if (isset($p['id'])) {
        $id=$p['id'];
        if (!isset($bulbs[$id])) {
            $bulbs[$id]=new Yeelight\GenericDevice();
        }
        $bulbs[$id]->updateState($p);
        $shm->setVar(YEELIGHT_SHM_CODE,$bulbs);
        $actions=$bulbs[$id]->getActions();
        if(!is_null($actions)) {
            #file_put_contents('http://127.0.0.1:81/action/?module=yeelight&id='.$id,$actions);
            echo date('c').' '.$id.'=>'.$actions.PHP_EOL;
        }
    }
} while (1);
