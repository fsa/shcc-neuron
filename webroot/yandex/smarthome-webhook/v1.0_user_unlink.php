<?php
/**
 * https://yandex.ru/dev/dialogs/alice/doc/smart-home/reference/unlink-docpage/
 */
if(!isset($request_id)) {die;}
OAuth\Server::grantAccess();
if(OAuth\Server::revoke()) {
    App::response()->json(['request_id'=>$request_id]);
}
App::response()->returnError(500);
