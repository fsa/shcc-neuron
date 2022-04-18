<?php

require_once '../../vendor/autoload.php';
$server=new OAuth\Server();
switch (filter_input(INPUT_GET, 'response_type')) {
    case 'code':
        App::initHtml();
        App::session()->grantAccess();
        App::response()->redirection($server->requestTypeCode(App::session()->getUserId(), ['email']));
        exit;
#    case 'token':
#        App::response()->redirection($server->requestTypeToken());
#        exit;
}
switch (filter_input(INPUT_POST, 'grant_type')) {
    case 'authorization_code':
        App::initJson();
        App::response()->json($server->grantTypeAuthorizationCode());
        exit;
    case 'refresh_token':
        App::initJson();
        App::response()->json($server->grantTypeRefreshToken());
        exit;
#    case 'password':
#        App::initJson();
#        App::response()->json($server->grantTypePassword());
#        exit;
#    case 'client_credentials':
#        App::initJson();
#        App::response()->json($server->grantTypeClientCredentials());
#        exit;
}
App::response()->returnError(400);