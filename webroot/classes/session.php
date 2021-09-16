<?php

class Session {

    private static $_session;
    private $cookie_session;
    private $cookie_session_time=1800;
    private $cookie_token;
    private $cookie_token_time=2592000;
    private $cookie_params;
    private $session;
    private $name;

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
        if (is_null($session->session)) {
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
        $this->name=getenv('APP_NAME')?getenv('APP_NAME'):'neuron';
        $name=getenv('SESSION_NAME')?getenv('SESSION_NAME').'_':'';
        $this->cookie_session=$name.'access_token';
        $this->cookie_token=$name.'refresh_token';
        $this->cookie_params=[
            'path'=>getenv('SESSION_PATH')?getenv('SESSION_PATH'):'/',
            'domain'=>getenv('SESSION_DOMAIN')?getenv('SESSION_DOMAIN'):'',
            'secure'=>getenv('SESSION_SECURE')?getenv('SESSION_SECURE'):false,
            'httponly'=>true,
            'samesite'=>'Strict'
        ];
        $session_cookie=filter_input(INPUT_COOKIE, $this->cookie_session);
        if ($session_cookie) {
            $this->session=$this->storageGetSession($session_cookie);
            if (isset($this->session->revoke_token)) {
                $this->revokeToken($this->session->revoke_token);
                unset($this->session->revoke_token);
                $this->storageSetSession($session_cookie, $this->session, $this->storageGetSessionTtl($session_cookie));
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
        foreach ($this->storageGetRevokeTokens($token) as $revoke_token) {
            if ($revoke_token!=$current_token) {
                $this->revokeToken($revoke_token);
            }
        }
        $this->storageDelToken($token);
        $this->storageDelRevokeTokens($token);
    }

    private function restoreSession() {
        $token=filter_input(INPUT_COOKIE, $this->cookie_token);
        if (!$token) {
            return false;
        }
        $session=$this->storageGetToken($token);
        if (!$session) {
            $this->dropTokenCookie();
            return false;
        }
        if (isset($session->session_time)) {
            $this->cookie_session_time=$session->session_time;
        }
        if (isset($session->token_time)) {
            $this->cookie_token_time=$session->token_time;
        }
        if (isset($session->revoke_token)) {
            $this->revokeToken($session->revoke_token);
            unset($session->revoke_token);
            $this->storageSetToken($token, $session);
        }
        if (!isset($session->class) or!isset($session->args)) {
            $this->dropTokenCookie;
            $this->storageDelToken($token);
            return false;
        }
        $class_name=$session->class;
        $user=new $class_name(...$session->args);
        if (!$user->validate()) {
            $this->dropTokenCookie();
            $this->storageDelToken($token);
            return false;
        }
        $session_token=$this->generateRandomString();
        $new_token=$this->generateRandomString();
        $this->session=$user;
        $user->revoke_token=$token;
        $user->refresh_token=$new_token;
        $this->storageSetSession($session_token, $user);
        $this->storageAddRevokeToken($token, $new_token);
        $this->storageSetToken($new_token, ['revoke_token'=>$token, 'class'=>get_class($user), 'args'=>$user->getConstructorArgs(), 'browser'=>getenv('HTTP_USER_AGENT'), 'ip'=>getenv('REMOTE_ADDR'), 'session_time'=>$this->cookie_session_time, 'token_time'=>$this->cookie_token_time]);
        $this->setSessionCookie($session_token);
        $this->setTokenCookie($new_token);
        return true;
    }

    private function login($user, $session_time=1800, $token_time=2592000) {
        $old_token=filter_input(INPUT_COOKIE, $this->cookie_token);
        if ($old_token) {
            $this->revokeToken($old_token);
        }
        $this->cookie_session_time=$session_time;
        $this->cookie_token_time=$token_time;
        $session_token=$this->generateRandomString();
        $token=$this->generateRandomString();
        $this->session=['id'=>$user->getId(), 'login'=>$user->getLogin(), 'name'=>$user->getName(), 'email'=>$user->getEmail(), 'scope'=>$user->getScope(), 'refresh_token'=>$token];
        $this->storageSetSession($session_token, $user);
        $this->storageSetToken($token, ['class'=>get_class($user), 'args'=>$user->getConstructorArgs(), 'browser'=>getenv('HTTP_USER_AGENT'), 'ip'=>getenv('REMOTE_ADDR'), 'session_time'=>$session_time, 'token_time'=>$token_time]);
        $this->setSessionCookie($session_token);
        $this->setTokenCookie($token);
    }

    private function logout() {
        $this->user=null;
        $session_cookie=filter_input(INPUT_COOKIE, $this->cookie_session);
        if ($session_cookie) {
            $this->storageDelSession($session_cookie);
            $this->dropSessionCookie();
        }
        $token=filter_input(INPUT_COOKIE, $this->cookie_token);
        if ($token) {
            $this->revokeToken($token);
            $this->storageDelToken($token);
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
        $params['expires']=time()+$this->cookie_session_time;
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


    public function storageGetSession(string $token) {
        $session=json_decode(DBRedis::get($this->name.':session:'.$token));
        if (!$session) {
            return null;
        }
        return $session;
    }

    public function storageSetSession(string $token, $data, $ttl=null) {
        DBRedis::setEx($this->name.':session:'.$token, $ttl?$ttl:$this->cookie_session_time, json_encode($data));
    }

    public function storageGetSessionTtl(string $token) {
        return DBRedis::ttl($this->name.':session:'.$token);
    }

    public function storageDelSession($token) {
        DBRedis::del($this->name.':session:'.$token);
    }

    public function storageGetToken(string $token) {
        $session=json_decode(DBRedis::get($this->name.':session:token:'.$token));
        if (!$session) {
            return null;
        }
        return $session;
    }

    public function storageSetToken(string $token, $data) {
        DBRedis::setEx($this->name.':session:token:'.$token, $this->cookie_token_time, json_encode($data));
    }

    public function storageDelToken($token) {
        DBRedis::del($this->name.':session:token:'.$token);
    }

    public function storageGetRevokeTokens($token) {
        $tokens=json_decode(DBRedis::get($this->name.':session:token:'.$token.':revoke'), true);
        if (!is_array($tokens)) {
            $tokens=[];
        }
        return $tokens;
    }

    public function storageAddRevokeToken($token, $new_token) {
        $old=$this->storageGetRevokeTokens($token);
        $old[]=$new_token;
        DBRedis::setEx($this->name.':session:token:'.$token.':revoke', $this->cookie_token_time, json_encode($old));
    }

    public function storageDelRevokeTokens($token) {
        DBRedis::del($this->name.':session:token:'.$token.':revoke');
    }

}
