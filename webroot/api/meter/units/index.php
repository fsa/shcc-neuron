<?php

require_once '../../../common.php';
httpResponse::setJsonExceptionHandler();
Session::grantAccess();
$units=SmartHome\Meters::getUnits();
httpResponse::json($units, JSON_UNESCAPED_UNICODE|JSON_NUMERIC_CHECK);
