<?php

/**
 * OAuth 2.0 Server
 */

namespace Auth;

use DB,
    DBRedis;

class Server {

    const CODE_EXPIRED_IN=3600;
    const ACCESS_TOKEN_EXPIRED_IN=3600;
    const REFRESH_TOKEN_EXPIRED_IN=2592000;

    private $storage;

    public static $client;

    public static function grantAccess(array $scope=null): void {
        $bearer=getenv('HTTP_AUTHORIZATION');
        if (!preg_match('/Bearer\s(\S+)/', $bearer, $matches)) {
            header('WWW-Authenticate: Bearer realm="The access token required"');
            throw new AppException('The access token required', 401);
        }
        $token=self::fetchTokensByAccessToken($matches[1]);
        if (!$token) {
            header('WWW-Authenticate: Bearer error="invalid_token",error_description="Invalid access token"');
            throw new AppException('Invalid access token', 401);
        }
        if($token->expired) {
            header('WWW-Authenticate: Bearer error="invalid_token",error_description="The access token expired"');
            throw new AppException('The access token expired', 401);
        }
        if(!is_null($scope)) {
            #TODO проверить права доступа
            if(0) {
                header('WWW-Authenticate: Bearer error="insufficient_scope",error_description="The request requires higher privileges than provided by the access token."');
                throw new AppException('The request requires higher privileges than provided by the access token.', 403);
            }
        }
        self::$client=$token;
    }

    public static function getUserId() {
        return self::$client->user_id;
    }

    public function __construct() {
        $name=$name=getenv('APP_NAME')?getenv('APP_NAME'):'neuron';
        $this->storage=new class($name) {
            private $name;

            public function __construct($name) {
                $this->name=$name;
            }

            public function addCode($code, $data) {
                DBRedis::setEx($this->name.':oauth:code:'.$code, Server::CODE_EXPIRED_IN, json_encode($data));
            }

            public function getCode($code) {
                $code_info=json_decode(DBRedis::get($this->name.':oauth:code:'.$code));
                DBRedis::del($this->name.':oauth:code'.$code);
                return $code_info?$code_info:null;
            }
        };
    }

    /**
     * GET response_type=code
     */
    public function requestTypeCode($user, array $valid_scope=[]) {
        $client_id=filter_input(INPUT_GET, 'client_id');
        $redirect_uri=filter_input(INPUT_GET, 'redirect_uri');
        $scope=filter_input(INPUT_GET, 'scope');
        $state=filter_input(INPUT_GET, 'state');
        $client=$this->fetchClient($client_id);
        $response_state=($state!==false)?['state'=>$state]:['state'=>'test'];
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
        if($scope!==false) {
            foreach (explode(',', $scope) as $item) {
                if (array_search($item, $valid_scope)===false) {
                    return $redirect_uri.'?'.http_build_query(array_merge(['error'=>'invalid_scope', 'error_description'=>'The requested scope is invalid'],$response_state));
                }
            } 
        } else {
            $scope=null;
        }
        $code=$this->genCode();
        $this->storage->addCode($code, [$client->uuid, $user->getId(), $scope]);
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
    public function grantTypeAuthorizationCode() {
        $code=filter_input(INPUT_POST, 'code');
        $redirect_uri=filter_input(INPUT_POST, 'redirect_uri');
        $client_id=filter_input(INPUT_POST, 'client_id');
        $client_secret=filter_input(INPUT_POST, 'client_secret');
        $client=$this->fetchClient($client_id);
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

        
        $s=DB::prepare('SELECT t.code, t.scope, s.client_id, s.client_secret, CASE WHEN redirect_uris IS NULL THEN TRUE ELSE ?=ANY (s.redirect_uris) END AS safe_uri FROM oauth_tokens t LEFT JOIN oauth_clients s ON t.client_uuid=s.uuid WHERE code=? AND access_token IS NULL');
        $s->execute([$redirect_uri, $code]);
        $auth_tokens=$s->fetchObject();
        $s->closeCursor();
        if (!$auth_tokens) {
            throw new AppException('invalid_grant',400);
        }
        if (
            $auth_tokens->client_id!=$client_id or
            $auth_tokens->client_secret!=$client_secret or!$auth_tokens->safe_uri
        ) {
            $s=DB::prepare('DELETE FROM auth_tokens WHERE code=?');
            $s->execute([$code]);
            $s->closeCursor();
            throw new AppException('invalid_grant',400);
        }
        $access_token=$this->genAccessToken();
        $refresh_token=$this->genRefreshToken();
        $s=DB::prepare('UPDATE oauth_tokens SET access_token=?, token_expires_on=NOW()+interval \''.self::ACCESS_TOKEN_EXPIRED_IN.' seconds\', refresh_token=?, updated=NOW() WHERE code=? AND access_token IS NULL');
        $s->execute([$access_token, $refresh_token, $code]);
        return [
            "access_token"=>$access_token,
            "token_type"=>"bearer",
            "expires_in"=>self::ACCESS_TOKEN_EXPIRED_IN,
            "refresh_token"=>$refresh_token,
            "scope"=>$auth_tokens->scope
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
        $client_id=filter_input(INPUT_POST, 'client_id');
        $client_secret=filter_input(INPUT_POST, 'client_secret');
        $client=$this->fetchClient($client_id);
        if (!$client or $client->client_secret!=$client_secret) {
            throw new AppException('unauthorized_client', 401);
        }
        $old_refresh_token=filter_input(INPUT_POST, 'refresh_token');
        $access_token=$this->genAccessToken();
        $refresh_token=$this->genRefreshToken();
        $token=$this->fetchTokensByRefreshToken($old_refresh_token);
        if(!$token) {
            throw new AppException('invalid_grant', 400);
        }
        $s=DB::prepare('UPDATE oauth_tokens SET access_token=?, token_expires_on=NOW()+interval \''.self::ACCESS_TOKEN_EXPIRED_IN.' seconds\', updated=NOW(), refresh_token=? WHERE refresh_token=? RETURNING scope');
        $s->execute([$access_token, $refresh_token, $old_refresh_token]);
        $scope=$s->fetchColumn();
        return [
            "access_token"=>$access_token,
            "token_type"=>"bearer",
            "expires_in"=>self::ACCESS_TOKEN_EXPIRED_IN,
            "refresh_token"=>$refresh_token,
            "scope"=>$scope
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

    private function fetchClient($client_id) {
        $s=DB::prepare('SELECT uuid, client_id, client_secret, array_to_json(redirect_uris) AS redirect_uris FROM oauth_clients WHERE client_id=?');
        $s->execute([$client_id]);
        return $s->fetchObject();
    }
    
    public static function fetchTokensByAccessToken($access_token) {
        $s=DB::prepare('SELECT *, (updated_on<NOW()) AS expired FROM oauth_tokens WHERE access_token=?');
        $s->execute([$access_token]);
        return $s->fetchObject();
    }

    public function fetchTokensByRefreshToken($refresh_token) {
        $s=DB::prepare('SELECT * FROM oauth_tokens WHERE refresh_token=?');
        $s->execute([$refresh_token]);
        return $s->fetchObject();
    }
    
    public static function refreshAccessToken($old_access_token) {
        $access_token=$this->genAccessToken();
        $refresh_token=$this->genRefreshToken();
        $s=DB::prepare('UPDATE auth_tokens SET access_token=?, updated=NOW() WHERE access_token=?');
        $s->execute([$access_token, $refresh_token, $old_access_token]);
        if ($s->rowCount()==0) {
            throw new AppException('invalid_grant', 400);
        }        
    }

    public static function revoke(): bool {
        $s=DB::prepare('DELETE FROM auth_tokens WHERE access_token=?');
        $s->execute([self::$token->access_token]);
        return ($s->rowCount()==1)?true:false;
    }

}
