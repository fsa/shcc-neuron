<?php
/**
 * https://yandex.ru/dev/dialogs/alice/doc/smart-home/reference/post-devices-query-docpage/
 */
if(!isset($request_id)) {die;}
OAuth\Server::grantAccess();
$request=json_decode($request_content);
$devices=[];
foreach ($request->devices as $device) {
    $yandex_device=Yandex\SmartHome\Devices::getByUid($device->id);
    if(!$yandex_device) {
        continue;
    }
    # TODO вернуть состояние устройства
    $devices[]=$entity;
}
httpResponse::json([
    'request_id'=>$request_id,
    'payload'=>[
        'devices'=>$devices
    ]
]);