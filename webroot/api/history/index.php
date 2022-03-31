<?php
use FSA\Neuron\HttpResponse,
    FSA\Neuron\Session;
require_once '../../common.php';
HttpResponse::setJsonMode();
Session::grantAccess();
$uid=filter_input(INPUT_GET, 'uid');
$from=filter_input(INPUT_GET, 'from');
$to=filter_input(INPUT_GET, 'to');
HttpResponse::jsonString(SmartHome\Sensors::getHistoryJson($uid, $from, $to));
