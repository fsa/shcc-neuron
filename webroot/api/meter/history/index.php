<?php

require_once '../../../common.php';
httpResponse::setModeJson();
Auth\Session::grantAccess();
$uid=filter_input(INPUT_GET,'uid');
$from=filter_input(INPUT_GET,'from');
$to=filter_input(INPUT_GET,'to');
httpResponse::json(SmartHome\Meters::getHistory($uid, $from, $to), JSON_UNESCAPED_UNICODE|JSON_NUMERIC_CHECK);
