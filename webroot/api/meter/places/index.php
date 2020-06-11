<?php

require_once '../../../common.php';
httpResponse::setModeJson();
Auth\Session::grantAccess();
$unit=filter_input(INPUT_GET,'unit', FILTER_VALIDATE_INT);
if(!is_int($unit)) {
    httpResponse::json(['error'=>'Unit require']);
}
$result=\SmartHome\MeterUnits::getUnitById($unit);
$result->places=\SmartHome\Meters::getMetersPlaces($unit);
httpResponse::json($result);