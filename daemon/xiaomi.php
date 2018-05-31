<?php

require 'autoloader.php';
$xiaomi=new Xiaomi\SocketServer();
do {
    $pkt=$xiaomi->getPacket();
    var_dump($pkt->getAllData(),$pkt->getPeer());
    $filename='log/'.$pkt->getSid().'.log';
    file_put_contents($filename,date('c').PHP_EOL.print_r($pkt,true),FILE_APPEND);
} while (1);
