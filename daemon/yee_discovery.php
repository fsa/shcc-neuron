<?php

require 'autoloader.php';

$yeelight=new Yeelight\SocketServer();
$yeelight->run();
$yeelight->sendDiscover();