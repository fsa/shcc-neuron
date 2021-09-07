<?php

use DBRedis,
    AppException;

class Session {

    private static $_session;
    private $cookie_session;
    private $cookie_session_time;
    private $cookie_token;
    private $cookie_token_time;
    private $cookie_params;
    private $session_storage;
    private $session;

    public static function getInstance(): self {
        if (!self::$_session) {
            self::$_session=new self;
        }
        return self::$_session;
    }

    public static function start($user) {
        $session=self::getInstance();
        $session->login($user);
    }

    public static function drop() {
        $session=self::getInstance();
        $session->logout();
    }

    public static function grantAccess(array $scope=null): void {
        $session=self::getInstance();
        if ($session->checkAccess($scope)) {
            return;
        }
        if (is_null($session->user)) {
            throw new AppException(null, 401);
        }
        throw new AppException(null, 403);
    }

    public static function memberOf(array $scope=null): bool {
        $session=self::getInstance();
        return $session->checkAccess($scope);
    }

    public static function getUserId() {
        $session=self::getInstance();
        if (isset($session->session)) {
            return $session->session->uuid;
        }
        return null;
    }

    public static function getUserLogin() {
        $session=self::getInstance();
        if (isset($session->session)) {
            return $session->session->login;
        }
        return null;
    }

    public static function getUserName() {
        $session=self::getInstance();
        if (isset($session->session)) {
            return $session->session->name;
        }
        return null;
    }

    private function __construct() {
        $app_name=getenv('APP_NAME')?getenv('APP_NAME'):'neuron';
        $name=getenv('SESSION_NAME')?getenv('SESSION_NAME').'_':'';
        $this->cookie_session=$name.'access_token';
        $this->cookie_session_time=getenv('SESSION_TIME')?getenv('SESSION_TIME'):1800;
        $this->cookie_token=$name.'refresh_token';
        $this->cookie_token_time=getenv('SESSION_TOKEN_TIME')?getenv('SESSION_TOKEN_TIME'):2592000;
        $this->cookie_params=[
            'path'=>getenv('SESSION_PATH')?getenv('SESSION_PATH'):'/',
            'domain'=>getenv('SESSION_DOMAIN')?getenv('SESSION_DOMAIN'):'',
            'secure'=>getenv('SESSION_SECURE')?getenv('SESSION_SECURE'):false,
            'httponly'=>true,
            'samesite'=>'Strict'
        ];
        $this->session_storage=$this->getSessionSrotrage($app_name, $this->cookie_session_time, $this->cookie_token_time);
        $session_cookie=filter_input(INPUT_COOKIE, $this->cookie_session);
        if ($session_cookie) {
            $this->session=$this->session_storage->getSession($session_cookie);
            if (isset($this->session->revoke_token)) {
                $this->revokeToken($this->session->revoke_token);
                unset($this->session->revoke_token);
                $this->session_storage->setSession($session_cookie, $this->session);
            }
            if (isset($this->session)) {
                return;
            }
            if ($this->restoreSession()) {
                return;
            }
            $this->dropSessionCookie();
            #TODO дефолтное значение для $this->session
            return;
        }
        if ($this->restoreSession()) {
            return;
        }
    }

    private function revokeToken($token) {
        $current_token=filter_input(INPUT_COOKIE, $this->cookie_token);
        foreach ($this->session_storage->getRevokeTokens($token) as $revoke_token) {
            if ($revoke_token!=$current_token) {
                $this->session_storage->delToken($revoke_token);
            }
        }
        $this->session_storage->delToken($token);
        $this->session_storage->delRevokeTokens($token);
    }

    private function restoreSession() {
        $token=filter_input(INPUT_COOKIE, $this->cookie_token);
        if (!$token) {
            return false;
        }
        $session=$this->session_storage->getToken($token);
        if (!$session) {
            $this->dropTokenCookie();
            return false;
        }
        if (isset($session->revoke_token)) {
            $this->revokeToken($session->revoke_token);
            unset($session->revoke_token);
            $this->session_storage->setToken($token, $session);
        }
        if (!isset($session->class) or!isset($session->args)) {
            $this->dropTokenCookie;
            $this->session_storage->delToken($token);
            return false;
        }
        $class_name=$session->class;
        $user=new $class_name(...$session->args);
        if (!$user->validate()) {
            $this->dropTokenCookie();
            $this->session_storage->delToken($token);
            return false;
        }
        $session_token=$this->generateRandomString();
        $this->setSessionCookie($session_token);
        $this->session=$user;
        $user->revoke_token=$token;
        $this->session_storage->setSession($session_token, $user);
        $new_token=$this->generateRandomString();
        $this->session_storage->addRevokeToken($token, $new_token);
        $this->session_storage->setToken($new_token, ['revoke_token'=>$token, 'class'=>get_class($user), 'args'=>$user->getConstructorArgs(), 'browser'=>getenv('HTTP_USER_AGENT'), 'ip'=>getenv('REMOTE_ADDR')]);
        $this->setTokenCookie($new_token);
        return true;
    }

    private function login($user) {
        $old_token=filter_input(INPUT_COOKIE, $this->cookie_token);
        if ($old_token) {
            $this->revokeToken($old_token);
        }
        $session_token=$this->generateRandomString();
        $this->setSessionCookie($session_token);
        $this->session=['id'=>$user->getId(), 'login'=>$user->getLogin(), 'name'=>$user->getName(), 'email'=>$user->getEmail(), 'scope'=>$user->getScope()];
        $this->session_storage->setSession($session_token, $user);
        $token=$this->generateRandomString();
        $this->session_storage->setToken($token, ['class'=>get_class($user), 'args'=>$user->getConstructorArgs(), 'browser'=>getenv('HTTP_USER_AGENT'), 'ip'=>getenv('REMOTE_ADDR')]);
        $this->setTokenCookie($token);
    }

    private function logout() {
        $this->user=null;
        $session_cookie=filter_input(INPUT_COOKIE, $this->cookie_session);
        if ($session_cookie) {
            $this->session_storage->delSession($session_cookie);
            $this->dropSessionCookie();
        }
        $token=filter_input(INPUT_COOKIE, $this->cookie_token);
        if ($token) {
            $this->session_storage->delToken($token);
            $this->dropTokenCookie();
        }
    }

    private function checkAccess(?array $scope): bool {
        if (is_null($this->session)) {
            return false;
        }
        if (is_null($scope)) {
            return true;
        }
        if (getenv('APP_ADMINS')) {
            $admins=explode(',', getenv('APP_ADMINS'));
            if (array_search($this->session->login, $admins)!==false) {
                return true;
            }
        }
        if (isset($this->session->scope)) {
            foreach ($scope AS $item) {
                if (array_search($item, $this->session->scope)!==false) {
                    return true;
                }
            }
        }
        return false;
    }

    private function generateRandomString(int $length=32): string {
        $symbols='abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ01234567890';
        $max_index=strlen($symbols)-1;
        $string='';
        for ($i=0; $i<$length; $i++) {
            $string.=$symbols[rand(0, $max_index)];
        }
        return $string;
    }

    private function setSessionCookie(string $token): void {
        $params=$this->cookie_params;
        setcookie($this->cookie_session, $token, $params);
    }

    private function dropSessionCookie(): void {
        $params=$this->cookie_params;
        $params['expires']=1;
        setcookie($this->cookie_session, '', $params);
    }

    private function setTokenCookie(string $token): void {
        $params=$this->cookie_params;
        $params['expires']=time()+$this->cookie_token_time;
        setcookie($this->cookie_token, $token, $params);
    }

    private function dropTokenCookie(): void {
        $params=$this->cookie_params;
        $params['expires']=1;
        setcookie($this->cookie_token, '', $params);
    }

    private function getSessionSrotrage($name, $time, $refresh_time) {
        return new class($name, $time, $refresh_time) {

            private $name;
            private $session_time;
            private $refresh_time;

            public function __construct($name, $time, $refresh_time) {
                $this->name=$name;
                $this->session_time=$time;
                $this->refresh_time=$refresh_time;
            }

            public function getSession(string $token) {
                $session=json_decode(DBRedis::get($this->name.':session:'.$token));
                if (!$session) {
                    return null;
                }
                return $session;
            }

            public function setSession(string $token, $data) {
                DBRedis::setEx($this->name.':session:'.$token, $this->session_time, json_encode($data));
            }

            public function delSession($token) {
                DBRedis::del($this->name.':session:'.$token);
            }

            public function getToken(string $token) {
                $session=json_decode(DBRedis::get($this->name.':session:token:'.$token));
                if (!$session) {
                    return null;
                }
                return $session;
            }

            public function setToken(string $token, $data) {
                DBRedis::setEx($this->name.':session:token:'.$token, $this->refresh_time, json_encode($data));
            }

            public function delToken($token) {
                DBRedis::del($this->name.':session:token:'.$token);
            }

            public function getRevokeTokens($token) {
                $tokens=json_decode(DBRedis::get($this->name.':session:revoke:'.$token), true);
                if (!is_array($tokens)) {
                    $tokens=[];
                }
                return $tokens;
            }

            public function addRevokeToken($token, $new_token) {
                $old=$this->getRevokeTokens($token);
                $old[]=$new_token;
                DBRedis::setEx($this->name.':session:revoke:'.$token, $this->refresh_time, json_encode($old));
            }

            public function delRevokeTokens($token) {
                DBRedis::del($this->name.':session:revoke:'.$token);
            }
        };
    }

}
