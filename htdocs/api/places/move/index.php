<?php

require_once '../../../common.php';
Auth\Session::grantAccess([]);
$place_id=filter_input(INPUT_GET, 'place_id', FILTER_VALIDATE_INT);
$parent=filter_input(INPUT_GET, 'parent', FILTER_VALIDATE_INT);
if(!$place_id or !$parent) {
    httpResponse::json(['error'=>'Неверные параметры запроса']);
}
SmartHome\Places::move($place_id, $parent);
httpResponse::json(['result'=>'ok']);