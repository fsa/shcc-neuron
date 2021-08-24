<?php
require_once '../../../common.php';
httpResponse::setJsonExceptionHandler();
Session::grantAccess();
$uid=filter_input(INPUT_GET,'uid');
$from=filter_input(INPUT_GET,'from');
$to=filter_input(INPUT_GET,'to');
httpResponse::jsonString(SmartHome\Meters::getHistoryJson($uid, $from, $to));
