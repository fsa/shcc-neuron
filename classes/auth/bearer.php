<?php

namespace Auth;

use httpResponse;

class Bearer {

    private static $access_token;
    private static $user_id;

    public static function grantAccess() {
        $bearer=getenv('HTTP_AUTHORIZATION');
        if (!preg_match('/Bearer\s(\S+)/', $bearer, $matches)) {
            header('WWW-Authenticate: Bearer realm="The access token required"');
            httpResponse::error(401);
            exit;
        }
        $token=Server::fetchTokensByAccessToken($matches[1]);
        if (!$token) {
            header('WWW-Authenticate: Bearer error="invalid_token",error_description="Invalid access token"');
            httpResponse::error(401);
            exit;
        }
        if($token->expired) {
            header('WWW-Authenticate: Bearer error="invalid_token",error_description="The access token expired"');
            httpResponse::error(401);
            exit;
        }
        self::$access_token=$token->access_token;
        self::$user_id=$token->user_id;
    }

    public static function getUserId() {
        return self::$user_id;
    }

    public static function getAccessToken() {
        return self::$access_token;
    }

}
