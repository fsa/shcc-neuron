<?php

require_once '../../../common.php';
httpResponse::setModeJson();
Auth\Session::grantAccess();
$units=SmartHome\Meters::getUnits();
httpResponse::json($units, JSON_UNESCAPED_UNICODE|JSON_NUMERIC_CHECK);
