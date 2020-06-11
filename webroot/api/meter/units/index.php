<?php

require_once '../../../common.php';
httpResponse::setModeJson();
Auth\Session::grantAccess();
$units=SmartHome\MeterUnits::getUnits();
httpResponse::json($units, JSON_UNESCAPED_UNICODE|JSON_NUMERIC_CHECK);
