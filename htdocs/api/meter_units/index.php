<?php

require_once '../../common.php';
Auth\Internal::grantAccess();
$units=SmartHome\MeterUnits::getUnits();
foreach($units as $key=>&$unit) {
    $unit['meters']=SmartHome\Meters::getMetersByUnitId($key);
}
header('Content-Type: application/json');
echo json_encode($units,JSON_UNESCAPED_UNICODE|JSON_NUMERIC_CHECK);