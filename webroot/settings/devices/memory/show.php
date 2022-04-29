<?php

if (!isset($hwid)) {
    die;
}
$mem_device=SmartHome::deviceStorage()->get($hwid);
if($mem_device) {
    $response->redirection("../edit/?hwid=$hwid");
} else {
    $response->returnError(404, "Устройство с идентификатором '$hwid' не найдено.");
}
