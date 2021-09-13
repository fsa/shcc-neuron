<?php

/**
 * OAuth 2.0 Server
 */

namespace OAuth;

use DB,
    DBRedis;

class Server {

    const CODE_EXPIRED_IN=3600;
    const ACCESS_TOKEN_EXPIRED_IN=3600;
    const REFRESH_TOKEN_EXPIRED_IN=2592000;

    private $storage;

    public static $client;
    private static $realm;

    public static function setRealm($realm) {
        self::$realm=$realm;
    }

    public static function grantAccess(array $scope=null): void {
        if(!self::$realm) {
            self::$realm=getenv('APP_NAME')?getenv('APP_NAME'):'neuron';
        }
        $bearer=getenv('HTTP_AUTHORIZATION');
        if(!$bearer) {
            header('WWW-Authenticate: Bearer realm="'.self::$realm.'"');
            throw new AppException('Unauthorized', 401);
        }
        $list=explode(' ', $bearer);
        if(sizeof($list)!=2) {
            header('WWW-Authenticate: Bearer realm="'.self::$realm.'"');
            throw new AppException('Unauthorized', 401);
        }
        if(!strcasecmp($bearer, 'Bearer')) {
            header('WWW-Authenticate: Bearer realm="'.self::$realm.'"');
            throw new AppException('The access token required', 401);
        }
        $token_info=(new self)->storage->getAccessToken($list[1]);
        if (!$token_info) {
            header('WWW-Authenticate: Bearer realm="'.self::$realm.'",error="invalid_token",error_description="Invalid access token"');
            throw new AppException('Invalid access token', 401);
        }
        if(is_null($scope)) {
            self::$client=$token_info;
            return;
        }
        foreach(explode(',', $token_info->scope) as $item) {
            if(array_search($item, $scope)!==false) {
                self::$client=$token_info;
                return;
            }
        }
        header('WWW-Authenticate: Bearer realm="'.self::$realm.'",error="insufficient_scope",error_description="The request requires higher privileges than provided by the access token."');
        throw new AppException('The request requires higher privileges than provided by the access token.', 403);
    }

    public static function getUserId() {
        return self::$client->user_id;
    }

    public function __construct() {
        $name=getenv('APP_NAME')?getenv('APP_NAME'):'neuron';
        $this->storage=$this->getStorage($name);
    }

    /**
     * GET response_type=code
     */
    public function requestTypeCode($user_id, array $valid_scope=null) {
        $client_id=filter_input(INPUT_GET, 'client_id');
        $redirect_uri=filter_input(INPUT_GET, 'redirect_uri');
        $scope=filter_input(INPUT_GET, 'scope');
        $state=filter_input(INPUT_GET, 'state');
        $client=$this->getClient($client_id);
        $response_state=($state!==false)?['state'=>$state]:[];
        if($redirect_uri!==false) {
            $allow_uris=json_decode($client->redirect_uris);
            if($allow_uris and array_search($redirect_uri, $allow_uris)===false) {
                return $redirect_uri.'?'.http_build_query(array_merge(['error'=>'invalid_request', 'error_description'=>'redirect_uri is incorrect'],$response_state));
            }
        } else {
            return $redirect_uri.'?'.http_build_query(array_merge(['error'=>'invalid_request', 'error_description'=>'redirect_uri is missing'],$response_state));
        }
        if (!$client) {
            return $redirect_uri.'?'.http_build_query(array_merge(['error'=>'invalid_request', 'error_description'=>'client_id is incorrect'],$response_state));
        }
        if(!is_null($valid_scope) and $scope) {
            foreach (explode(',', $scope) as $item) {
                if (array_search($item, $valid_scope)===false) {
                    return $redirect_uri.'?'.http_build_query(array_merge(['error'=>'invalid_scope', 'error_description'=>'The requested scope is invalid'],$response_state));
                }
            }
        } else {
            $scope=null;
        }
        $code=$this->genCode();
        $this->storage->setCode($code, ['client_uuid'=>$client->uuid, 'user_id'=>$user_id, 'redirect_uri'=>$redirect_uri, 'scope'=>$scope]);
        return $redirect_uri.'?'.http_build_query(array_merge(['code'=>$code],$response_state));
    }

    /**
     * GET response_type=token
     */
    public function requestTypeToken() {
        throw new AppException('Запрос токена не реализован.', 405);
    }

    /**
     * POST grant_type=authorization_code
     */
    public function grantTypeAuthorizationCode($token_type='bearer') {
        $code=filter_input(INPUT_POST, 'code');
        $redirect_uri=filter_input(INPUT_POST, 'redirect_uri');
        $client_id=filter_input(INPUT_POST, 'client_id');
        $client_secret=filter_input(INPUT_POST, 'client_secret');
        $code_info=$this->storage->getCode($code);
        if(!$code_info) {
            throw new AppException(json_encode(['error'=>'invalid_grant', 'error_description'=>'code is invalid, expired, revoked']) ,400);
        }
        $client=$this->getClient($client_id);
        if (!$client or $client->client_id!=$client_id or !password_verify($client_secret, $client->client_secret)) {
            throw new AppException(json_encode(['error'=>'invalid_client', 'error_description'=>'client_id is incorrect']), 400);
        }
        if($redirect_uri!==false) {
            if($code_info->redirect_uri!=$redirect_uri) {
                throw new AppException(json_encode(['error'=>'invalid_grant', 'error_description'=>'redirect_uri is incorrect']), 400);
            }
        } else {
            if(isset($code_info->redirect_uri)) {
                throw new AppException(json_encode(['error'=>'invalid_grant', 'error_description'=>'redirect_uri is missing']), 400);
            }
        }
        $access_token=$this->genAccessToken();
        $refresh_token=$this->genRefreshToken();
        $this->storage->setAccessToken($access_token, ['client_id'=>$client_id, 'user_id'=>$code_info->user_id, 'scope'=>$code_info->scope]);
        $this->storage->setRefreshToken($refresh_token, ['access_token'=>$access_token, 'token_type'=>$token_type, 'client_id'=>$client_id, 'user_id'=>$code_info->user_id, 'scope'=>$code_info->scope]);
        return [
            "access_token"=>$access_token,
            "token_type"=>$token_type,
            "expires_in"=>self::ACCESS_TOKEN_EXPIRED_IN,
            "refresh_token"=>$refresh_token,
            "scope"=>$code_info->scope
        ];
    }

    /**
     * POST grant_type=password
     */
    public function grantTypePassword() {
        throw new AppException('POST запрос grant_type=password не реализован.', 405);
    }

    /**
     * POST grant_type=refresh_token
     */
    public function grantTypeRefreshToken() {
        # Возможны другие типы аутентификации
        $client_id=filter_input(INPUT_POST, 'client_id');
        $client_secret=filter_input(INPUT_POST, 'client_secret');
        #$scope=filter_input(INPUT_POST, 'scope');
        # Только уменьшение scope
        $client=$this->getClient($client_id);
        if (!$client or $client->client_id!=$client_id or !password_verify($client_secret, $client->client_secret)) {
            throw new AppException(json_encode(['error'=>'invalid_client', 'error_description'=>'client_id is incorrect']), 400);
        }
        $old_refresh_token=filter_input(INPUT_POST, 'refresh_token');
        $token_info=$this->storage->getRefreshToken($old_refresh_token);
        if(!$token_info) {
            throw new AppException(json_encode(['error'=>'invalid_grant', 'error_description'=>'token is invalid, expired, revoked']) ,400);
        }
        $access_token=$this->genAccessToken();
        $refresh_token=$this->genRefreshToken();
        $this->storage->setAccessToken($access_token, ['client_id'=>$token_info->client_id, 'user_id'=>$token_info->user_id, 'scope'=>$token_info->scope]);
        $this->storage->setRefreshToken($refresh_token, ['access_token'=>$access_token, 'token_type'=>$token_info->token_type, 'client_id'=>$token_info->client_id, 'user_id'=>$token_info->user_id, 'scope'=>$token_info->scope]);
        return [
            "access_token"=>$access_token,
            "token_type"=>$token_info->token_type,
            "expires_in"=>self::ACCESS_TOKEN_EXPIRED_IN,
            "refresh_token"=>$refresh_token,
            "scope"=>$token_info->scope
        ];
    }

    /**
     * POST grant_type=client_credentials
     */
    public function grantTypeClientCredentials() {
        throw new AppException('Запрос ClientCredentials не реализован.', 405);
    }

    private function genCode(): string {
        return bin2hex(openssl_random_pseudo_bytes(16));
    }

    private function genAccessToken(): string {
        return $this->genRandomString(32);
    }

    private function genRefreshToken(): string {
        return $this->genRandomString(32);
    }

    private function genRandomString($length): string {
        $symbols='abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ01234567890';
        $max_index=strlen($symbols)-1;
        $string='';
        for ($i=0; $i<$length; $i++) {
            $index=rand(0, $max_index);
            $string.=$symbols[$index];
        }
        return $string;
    }

    private function getClient($client_id) {
        $s=DB::prepare('SELECT uuid, client_id, client_secret, array_to_json(redirect_uris) AS redirect_uris FROM oauth_clients WHERE client_id=?');
        $s->execute([$client_id]);
        return $s->fetchObject();
    }

    private function getStorage($name) {
        return new class($name) {
            private $name;

            public function __construct($name) {
                $this->name=$name;
            }

            public function setCode($code, $data) {
                DBRedis::setEx($this->name.':oauth:code:'.$code, Server::CODE_EXPIRED_IN, json_encode($data));
            }

            public function getCode($code) {
                $code_info=json_decode(DBRedis::get($this->name.':oauth:code:'.$code));
                DBRedis::del($this->name.':oauth:code:'.$code);
                return $code_info?$code_info:null;
            }

            public function setAccessToken($token, $data) {
                DBRedis::setEx($this->name.':oauth:access_token:'.$token, Server::ACCESS_TOKEN_EXPIRED_IN, json_encode($data));
            }

            public function getAccessToken($token) {
                $token_info=json_decode(DBRedis::get($this->name.':oauth:access_token:'.$token));
                return $token_info?$token_info:null;
            }


            public function setRefreshToken($token, $data) {
                DBRedis::setEx($this->name.':oauth:refresh_token:'.$token, Server::REFRESH_TOKEN_EXPIRED_IN, json_encode($data));
            }

            public function getRefreshToken($token) {
                $token_info=json_decode(DBRedis::get($this->name.':oauth:refresh_token:'.$token));
                return $token_info?$token_info:null;
            }

        };
    }

}