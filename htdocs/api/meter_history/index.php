<?php

require_once '../../common.php';
Auth\Internal::grantAccess();
$place_id=filter_input(INPUT_GET,'place',FILTER_VALIDATE_INT);
$meter_id=filter_input(INPUT_GET,'meter',FILTER_VALIDATE_INT);
$meter_unit_id=filter_input(INPUT_GET,'unit',FILTER_VALIDATE_INT);
$from=filter_input(INPUT_GET,'from');
$to=filter_input(INPUT_GET,'to');
try {
    $history=new SmartHome\MeterHistory();
    $history->setPlaceId($place_id,$meter_unit_id);
    $history->setMeterId($meter_id);
    $history->setFromDateTime($from);
    $history->setToDateTime($to);
    $result=$history->getHistory();
} catch (AppException $ex) {
    httpResponse::json(['error'=>$ex->getMessage()]);
} catch (Exception $ex) {
    httpResponse::json(['error'=>'Internal error']);
}
httpResponse::json($result, JSON_NUMERIC_CHECK);
