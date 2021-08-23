<?php
/**
 * https://yandex.ru/dev/dialogs/alice/doc/smart-home/reference/unlink-docpage/
 */
if(!isset($request_id)) {die;}
\Auth\Server::grantAccess();
if(Auth\Server::revoke()) {
    httpResponse::json(['request_id'=>$request_id]);
}
httpResponse::error(500, 'Internal Server Error');
