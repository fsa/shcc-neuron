<?php
/**
 * https://yandex.ru/dev/dialogs/alice/doc/smart-home/reference/unlink-docpage/
 */
use FSA\Neuron\HttpResponse;
if(!isset($request_id)) {die;}
OAuth\Server::grantAccess();
if(OAuth\Server::revoke()) {
    HttpResponse::json(['request_id'=>$request_id]);
}
HttpResponse::error(500, 'Internal Server Error');
