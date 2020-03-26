<?php

require_once '../../common.php';
httpResponse::setModeJson();
Auth\Session::grantAccess();
$units=SmartHome\MeterUnits::getUnits();
foreach($units as $key=>&$unit) {
    $unit['meters']=SmartHome\Meters::getMetersByUnitId($key);
}
httpResponse::json($result, JSON_UNESCAPED_UNICODE|JSON_NUMERIC_CHECK);
