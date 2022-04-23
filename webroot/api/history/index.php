<?php

use SmartHome\Sensors;

require_once '../../../vendor/autoload.php';
App::initJson();
App::session()->grantAccess();
$uid=filter_input(INPUT_GET, 'uid');
$from=filter_input(INPUT_GET, 'from');
$to=filter_input(INPUT_GET, 'to');
App::response()->jsonString(Sensors::getHistoryJson($uid, $from, $to));
