<?php

require 'autoloader.php';

$lamp1=new Yeelight\GenericDevice();
$lamp2=new Yeelight\GenericDevice();
$yeelight=new Yeelight\SocketServer();
$yeelight->run();
$yeelight->sendDiscover();
do {
    $pkt=$yeelight->getPacket();
    $p=$pkt->getParams();
    if($p['id']=='0x0000000005383a0a') {
        $lamp1->updateState($p);
        file_put_contents('lamp1',serialize($lamp1));
    }
    if($p['id']=='0x0000000005438b97') {
        $lamp2->updateState($p);        
        file_put_contents('lamp2',serialize($lamp2));
    }
} while (1);
