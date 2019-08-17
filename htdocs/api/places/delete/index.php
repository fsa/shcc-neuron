<?php

require_once '../../../common.php';
Auth\Internal::grantAccess(['admin']);
$id=filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if(!$id) {
    httpResponse::json(['error'=>'Неверные параметры запроса']);
}
SmartHome\Places::delete($id);
httpResponse::json(['result'=>'ok']);