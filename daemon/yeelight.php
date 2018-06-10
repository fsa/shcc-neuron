<?php

require 'autoloader.php';

const YEELIGHT_SHM_CODE=1;

$mem=new Shm();
$bulbs=$mem->getVar(YEELIGHT_SHM_CODE);
if ($devices===false) {
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
        #file_put_contents('yeelight/'.$id.'.yeelight',serialize($bulbs[$id]));
    }
    var_dump(date('c'),$pkt);
} while (1);
