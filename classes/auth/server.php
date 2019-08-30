<?php

/**
 * OAuth 2.0 Server
 */

namespace Auth;

use DB,
    PDO,
    httpResponse;

class Server {

    const ACCESS_TOKEN_EXPIRED_IN=3600;

    private $client_id;
    private $state;

    public function __construct() {
        $response_type=filter_input(INPUT_GET, 'response_type');
        if ($response_type) {
            $this->state=filter_input(INPUT_GET, 'state');
            $this->client_id=filter_input(INPUT_GET, 'client_id');
            if (!$this->client_id) {
                throw new ServerException('Не указан идентифиатор клиента');
            }
            switch ($response_type) {
                case 'code':
                    $this->requestGetCode();
                    break;
                case 'token':
                    $this->requestGetToken();
                    break;
            }
            throw new ServerException('Не поддерживаемый тип запроса');
        }
        $grant_type=filter_input(INPUT_POST, 'grant_type');
        if ($grant_type) {
            $this->client_id=filter_input(INPUT_POST, 'client_id');
            switch ($grant_type) {
                case 'authorization_code':
                    $this->requestPostAuthorizationCode();
                    break;
                case 'password':
                    $this->requestPostPassword();
                    break;
                case 'client_credentials':
                    $this->requestClientCredentials();
                    break;
                case 'refresh_token':
                    $this->requestPostRefreshToken();
                    break;
            }
            httpResponse::json(['error'=>'unsupported_response_type']);
        }
        throw new ServerException('Неверный запрос');
    }

    private function requestGetCode() {
        Internal::grantAccess();
        $client_id=filter_input(INPUT_GET, 'client_id');
        $redirect_uri=filter_input(INPUT_GET, 'redirect_uri');
        $scope=filter_input(INPUT_GET, 'scope');
        $client=$this->fetchClient($client_id);
        if (!$client) {
            httpResponse::json(['error'=>'unauthorized_client']);
        }
        $user_id=Internal::getUser()->id;
        $scope=User::checkScope($scope, $user_id);
        $code=$this->genCode();
        $s=DB::prepare('INSERT INTO auth_tokens (client_id, user_id, code, scope) VALUES (?, ?, ?, ?)');
        $s->execute([$client->id, $user_id, $code, $scope]);
        $s->closeCursor();
        $params=[
            'code'=>$code,
            'state'=>$this->state
        ];
        httpResponse::redirect($redirect_uri.'?'.http_build_query($params), 302, 'Found');
    }

    private function requestGetToken() {
        throw new ServerException('Не реализовано');
    }

    private function requestPostAuthorizationCode() {
        $code=filter_input(INPUT_POST, 'code');
        $redirect_uri=filter_input(INPUT_POST, 'redirect_uri');
        $s=DB::prepare('SELECT t.code, t.scope, s.client_id, s.client_secret, CASE WHEN redirect_uris IS NULL THEN TRUE ELSE ?=ANY (s.redirect_uris) END AS safe_uri FROM auth_tokens t LEFT JOIN auth_server s ON t.client_id=s.id WHERE code=? AND access_token IS NULL');
        $s->execute([$redirect_uri, $code]);
        $auth_tokens=$s->fetch(PDO::FETCH_OBJ);
        $s->closeCursor();
        if (!$auth_tokens) {
            httpResponse::json(['error'=>'invalid_grant']);
        }
        $client_id=filter_input(INPUT_POST, 'client_id');
        $client_secret=filter_input(INPUT_POST, 'client_secret');
        if (
            $auth_tokens->client_id!=$client_id or
            $auth_tokens->client_secret!=$client_secret or!$auth_tokens->safe_uri
        ) {
            $s=DB::prepare('DELETE FROM auth_tokens WHERE code=?');
            $s->execute([$code]);
            $s->closeCursor();
            httpResponse::json(['error'=>'invalid_grant']);
        }
        $access_token=$this->genAccessToken();
        $refresh_token=$this->genRefreshToken();
        $s=DB::prepare('UPDATE auth_tokens SET access_token=?, refresh_token=?, updated=NOW() WHERE code=? AND access_token IS NULL');
        $s->execute([$access_token, $refresh_token, $code]);
        $s->closeCursor();
        httpResponse::json([
            "access_token"=>$access_token,
            "token_type"=>"bearer",
            "expires_in"=>self::ACCESS_TOKEN_EXPIRED_IN,
            "refresh_token"=>$refresh_token,
            "scope"=>$auth_tokens->scope
        ]);
    }

    private function requestPostPassword() {
        $login=filter_input(INPUT_POST, 'username');
        $password=filter_input(INPUT_POST, 'password');
        $scope=filter_input(INPUT_POST, 'scope');
        $user=User::authenticate($login,$password);
        if(is_null($user)) {
            Fail2Ban::addFail($login);
            httpResponse::error(401);  
        }
        if(Fail2Ban::ipIsBlocked() or Fail2Ban::loginIsBlocked($login)) {
            httpResponse::error(429);
        }
        $scope=User::checkScope($scope, $user->getId());
        $code=$this->genCode();
        $access_token=$this->genAccessToken();
        $refresh_token=$this->genRefreshToken();
        $s=DB::prepare('INSERT INTO auth_tokens (user_id, code, access_token, refresh_token, scope) VALUES (?,?,?,?,?)');
        $s->execute([$user->getId(),$code, $access_token, $refresh_token,$scope]);
        $s->closeCursor();        
        httpResponse::json([
            "access_token"=>$access_token,
            "token_type"=>"bearer",
            "expires_in"=>self::ACCESS_TOKEN_EXPIRED_IN,
            "refresh_token"=>$refresh_token,
            "scope"=>$auth_tokens->scope
        ]);
    }

    private function requestPostRefreshToken() {
        $client_id=filter_input(INPUT_POST, 'client_id');
        $client_secret=filter_input(INPUT_POST, 'client_secret');
        $client=$this->fetchClient($client_id);
        if (!$client or $client->client_secret!=$client_secret) {
            httpResponse::json(['error'=>'unauthorized_client']);
        }
        $old_refresh_token=filter_input(INPUT_POST, 'refresh_token');
        $scope=filter_input(INPUT_POST, 'scope'); #TODO ?
        $access_token=$this->genAccessToken();
        $refresh_token=$this->genRefreshToken();
        $token=self::fetchTokensByRefreshToken($old_refresh_token);
        if(!$token) {
            httpResponse::json(['error'=>'invalid_grant']);            
        }
        if(is_null($scope)) {
            $s=DB::prepare('UPDATE auth_tokens SET access_token=?, updated=NOW(), refresh_token=? WHERE refresh_token=?');
            $s->execute([$access_token, $refresh_token, $old_refresh_token]);
        } else {
            $scope=User::checkScope($scope, $token->user_id);
            $s=DB::prepare('UPDATE auth_tokens SET access_token=?, updated=NOW(), refresh_token=?, scope=? WHERE refresh_token=?');
            $s->execute([$access_token, $refresh_token, $scope, $old_refresh_token]);          
        }
        $s->closeCursor();
        httpResponse::json([
            "access_token"=>$access_token,
            "token_type"=>"bearer",
            "expires_in"=>self::ACCESS_TOKEN_EXPIRED_IN,
            "refresh_token"=>$refresh_token,
            "scope"=>$scope
        ]);
    }

    private function requestPostClientCredentials() {
        httpResponse::json(['error'=>'unsupported_response_type']);
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
        $s=DB::prepare('SELECT * FROM auth_server WHERE client_id=?');
        $s->execute([$client_id]);
        return $s->fetch(PDO::FETCH_OBJ);
    }
    
    public static function fetchTokensByAccessToken($access_token) {
        $s=DB::prepare('SELECT *, (updated+expires_in<NOW()) AS expired FROM auth_tokens WHERE access_token=?');
        $s->execute([$access_token]);
        return $s->fetch(PDO::FETCH_OBJ);
    }

    public static function fetchTokensByRefreshToken($refresh_token) {
        $s=DB::prepare('SELECT * FROM auth_tokens WHERE refresh_token=?');
        $s->execute([$refresh_token]);
        return $s->fetch(PDO::FETCH_OBJ);
    }
    
    public static function refreshAccessToken($old_access_token) {
        $access_token=$this->genAccessToken();
        $refresh_token=$this->genRefreshToken();
        $s=DB::prepare('UPDATE auth_tokens SET access_token=?, updated=NOW() WHERE access_token=?');
        $s->execute([$access_token, $refresh_token, $old_access_token]);
        if ($s->rowCount()!=1) {
            httpResponse::json(['error'=>'invalid_grant']);
        }        
    }

    public static function revoke($access_token): bool {
        $s=DB::prepare('DELETE FROM auth_tokens WHERE access_token=?');
        $s->execute([$access_token]);
        return ($s->rowCount()==1)?true:false;
    }

}
