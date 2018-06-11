<?php

require 'autoloader.php';

$mem=new MemoryStorage();
$bulbs=$mem->getArray('yeelight');
$yeelight=new Yeelight\SocketServer();
$yeelight->run();
$yeelight->sendDiscover();
do {
    $pkt=$yeelight->getPacket();
    $p=$pkt->getParams();
    if (isset($p['id'])) {
        $id=$p['id'];
        if (!isset($bulbs[$id])) {
            $bulbs[$id]=new Yeelight\GenericDevice();
        }
        $bulbs[$id]->updateState($p);
        $mem->setVar('yeelight',$bulbs);
        $actions=$bulbs[$id]->getActions();
        if(!is_null($actions)) {
            #file_put_contents('http://127.0.0.1:81/action/?module=yeelight&id='.$id,$actions);
            echo date('c').' '.$id.'=>'.$actions.PHP_EOL;
        }
    }
} while (1);
