<?php

class Session {

    const USER='user';
    const REVOKE_TOKEN='user_revoke_token';

    private static $_session;
    private $cookie_session;
    private $cookie_token;
    private $cookie_time;
    private $cookie_params;
    private $session_storage;
    private $user;

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
        if(isset($session->user)) {
            return $session->user->getId();
        }
        return null;
    }

    public static function getUser() {
        return (self::getInstance())->user;
    }

    private function __construct() {
        $name=getenv('SESSION_NAME')?getenv('SESSION_NAME'):'neuron';
        $this->cookie_session=$name.'_session';
        $this->cookie_token=$name.'_token';
        $this->cookie_time=getenv('SESSION_TIME')?getenv('SESSION_TIME'):2592000;
        $this->cookie_params=[
            'path'=>getenv('SESSION_PATH')?getenv('SESSION_PATH'):'/',
            'domain'=>getenv('SESSION_DOMAIN')?getenv('SESSION_DOMAIN'):'',
            'secure'=>getenv('SESSION_SECURE')?getenv('SESSION_SECURE'):false,
            'httponly'=>true,
            'samesite'=>'Strict'
        ];
        $this->session_storage=new class ($name) {
            private $name;

            public function __construct($name) {
                $this->name=$name;
            }

            public function set(string $token, $data, int $time) {
                DBRedis::setEx($this->name.':session:'.$token, $time, json_encode($data));
            }

            public function get(string $token) {
                $session=json_decode(DBRedis::get($this->name.':session:'.$token));
                if(!$session) {
                    return null;
                }
                return $session;
            }

            public function del($token) {
                DBRedis::del($this->name.':session:'.$token);
            }

            public function revokeGet($token) {
                $tokens=json_decode(DBRedis::get($this->name.':session:revoke:'.$token), true);
                if(!is_array($tokens)) {
                    $tokens=[];
                }
                return $tokens;
            }

            public function revokeAdd($token, $new_token, int $time) {
                $old=$this->revokeGet($token);
                $old[]=$new_token;
                DBRedis::setEx($this->name.':session:revoke:'.$token, $time, json_encode($old));
            }

            public function revokeDel($token) {
                DBRedis::del($this->name.':session:revoke:'.$token);
            }
        };
        session_name($this->cookie_session);
        session_set_cookie_params($this->cookie_params);
        $session_cookie=filter_input(INPUT_COOKIE, $this->cookie_session);
        if ($session_cookie) {
            $this->phpSessionStart();
            if(isset($_SESSION[self::REVOKE_TOKEN])) {
                $this->revokeToken($_SESSION[self::REVOKE_TOKEN]);
                unset($_SESSION[self::REVOKE_TOKEN]);
            }
            if ($this->getPhpSessionUser()) {
                session_commit();
                return;
            }
            if ($this->restorePhpSession()) {
                session_commit();
                return;
            }
            $this->dropPhpSessionCookie();
            $this->dropLongSessionCookie();
            unset($_SESSION[self::USER]);
            session_commit();
            return;
        }
        if ($this->restorePhpSession()) {
            $_SESSION[self::REVOKE_TOKEN]=filter_input(INPUT_COOKIE, $this->cookie_token);
            session_commit();
            return;
        }
    }

    private function getPhpSessionUser(): bool {
        if (isset($_SESSION[self::USER])) {
            $this->user=$_SESSION[self::USER];
            return true;
        }
        return false;
    }

    private function revokeToken($token) {
        $current_token=filter_input(INPUT_COOKIE, $this->cookie_token);
        foreach($this->session_storage->revokeGet($token) as $revoke_token) {
            if($revoke_token!=$current_token) {
                $this->session_storage->del($revoke_token);
            }
        }
        $this->session_storage->del($token);
        $this->session_storage->revokeDel($token);
    }

    private function restorePhpSession() {
        $token=filter_input(INPUT_COOKIE, $this->cookie_token);
        if (!$token) {
            return false;
        }
        $session=$this->session_storage->get($token);
        if (!$session) {
            $this->dropLongSessionCookie();
            return false;
        }
        if(isset($session->old_token)) {
            $this->revokeToken($session->old_token);
        }
        if(!isset($session->class) or !isset($session->args)) {
            $this->session_storage->del($token);
            $this->dropLongSessionCookie();
            return false;
        }
        $class_name=$session->class;
        $user=new $class_name(...$session->args);
        if (!$user->validate()) {
            $this->session_storage->del($token);
            $this->dropLongSessionCookie();
            return false;
        }
        $this->phpSessionStart();
        $this->user=$user;
        $_SESSION[self::USER]=$user;
        $_SESSION[self::REVOKE_TOKEN]=$token;
        $new_token=$this->generateRandomString();
        $this->session_storage->revokeAdd($token, $new_token, $this->cookie_time);
        $this->session_storage->set($new_token, ['old_token'=>$token, 'class'=>get_class($user), 'args'=>$user->getConstructorArgs()], $this->cookie_time);
        $this->setCookie($new_token);
        return true;
    }

    private function login($user) {
        $old_token=filter_input(INPUT_COOKIE, $this->cookie_token);
        if ($old_token) {
            $this->session_storage->del($old_token);
        }
        $this->phpSessionStart();
        $this->user=$user;
        $_SESSION[self::USER]=$user;
        session_commit();
        $token=$this->generateRandomString();
        $this->session_storage->set($token, ['class'=>get_class($user), 'args'=>$user->getConstructorArgs()], $this->cookie_time);
        $this->setCookie($token);
    }

    private function logout() {
        $this->user=null;
        $session_cookie=filter_input(INPUT_COOKIE, $this->cookie_session);
        if ($session_cookie) {
            $this->phpSessionStart();
            unset($_SESSION[self::USER]);
            session_commit();
        }
        $this->dropPhpSessionCookie();
        $token=filter_input(INPUT_COOKIE, $this->cookie_token);
        if ($token) {
            $this->session_storage->del($token);
            $this->dropLongSessionCookie();
        }
    }

    private function checkAccess(?array $scope): bool {
        if (is_null($this->user)) {
            return false;
        }
        if (is_null($scope)) {
            return true;
        }
        if(getenv('SITE_ADMINS')) {
            $admins=explode(',', getenv('SITE_ADMINS'));
            if (array_search($this->user->login, $admins)!==false) {
                return true;
            }
        }
        if (isset($this->user->scope)) {
            foreach ($scope AS $item) {
                if (array_search($item, $this->user->scope)!==false) {
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

    private function setCookie(string $token): void {
        $params=$this->cookie_params;
        $params['expires']=time()+$this->cookie_time;
        setcookie($this->cookie_token, $token, $params);
    }

    private function phpSessionStart() {
        if (!session_start()) {
            throw new AppException('session_start() failed');
        }
    }

    private function dropLongSessionCookie(): void {
        $params=$this->cookie_params;
        $params['expires']=1;
        setcookie($this->cookie_token, '', $params);
    }

    private function dropPhpSessionCookie(): void {
        $params=$this->cookie_params;
        $params['expires']=1;
        setcookie($this->cookie_session, '', $params);
    }

}
