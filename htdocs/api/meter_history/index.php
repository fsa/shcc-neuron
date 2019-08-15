<?php

require_once '../../common.php';
Auth\Internal::grantAccess();
$place_id=filter_input(INPUT_GET,'place',FILTER_VALIDATE_INT);
$meter_id=filter_input(INPUT_GET,'meter',FILTER_VALIDATE_INT);
$meter_unit_id=filter_input(INPUT_GET,'unit',FILTER_VALIDATE_INT);
$from=filter_input(INPUT_GET,'from');
$to=filter_input(INPUT_GET,'to');
$history=new SmartHome\MeterHistory();
$history->setPlaceId($place_id,$meter_unit_id);
$history->setMeterId($meter_id);
$history->setFromDateTime($from);
$history->setToDateTime($to);
#TODO фильтрация по периоду
header('Content-Type: application/json');
echo $history->getJson();