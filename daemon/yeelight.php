<?php

require 'autoloader.php';
$bulbs=[];
$yeelight=new Yeelight\SocketServer();
$yeelight->run();
$yeelight->sendDiscover();
$shm=shm_attach(1,65536,600);
do {
    $pkt=$yeelight->getPacket();
    $p=$pkt->getParams();
    if (isset($p['id'])) {
        $id=$p['id'];
        if (!isset($bulbs[$id])) {
            $bulbs[$id]=new Yeelight\GenericDevice();
        }
        $bulbs[$id]->updateState($p);
        shm_put_var($shm,1,$bulbs);
        #file_put_contents('objects/'.$id.'.yeelight',serialize($bulbs[$id]));
    }
    var_dump(date('c'),$pkt);
} while (1);
