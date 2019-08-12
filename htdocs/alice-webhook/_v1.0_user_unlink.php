<?php
if(!isset($request_id)) {die;}
if(Auth\Server::revoke(Auth\Bearer::getAccessToken())) {
    httpResponse::json(['request_id'=>$request_id]);
}
header(getenv('SERVER_PROTOCOL').' 500 Internal Server Error');