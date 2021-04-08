<?php

namespace Auth;

use DB,
    PDO;

class Session {

    private static $_instance;
    private static $cookie;
    private $user;

    public static function grantAccess(array $scope=null): void {
        $auth=self::getInstance();
        if ($auth->checkAccess($scope)) {
            return;
        }
        if(!is_null($auth->user->getLogin())) {
            throw new AccessException();
        }
        throw new AuthException();
    }

    public static function memberOf(array $scope=null): bool {
        $auth=self::getInstance();
        return $auth->checkAccess($scope);
    }

    public static function login(UserInterface $user): void {
        $session=self::getCookieConfig();
        session_name($session['session']);
        session_set_cookie_params($session['params']);
        self::$_instance=new self;
        self::$_instance->user=$user;
        if (!session_start()) {
            throw new AppException('session_start failed.');
        }
        $_SESSION['user']=$user;
        session_commit();
        self::start($user);
    }

    public static function logout(): void {
        self::destroy();
        $session=self::getCookieConfig();
        session_name($session['session']);
        session_set_cookie_params($session['params']);
        if (!session_start()) {
            throw new AppException('session_start failed.');
        }
        $_SESSION=[];
        session_destroy();
        $params=$session['params'];
        $params['lifetime']=1;
        setcookie($session['session'], $params);
    }

    public static function getUser(): UserInterface {
        return self::getInstance()->user;
    }

    private static function getInstance(): self {
        if (is_null(self::$_instance)) {
            self::$_instance=new self;
            self::$_instance->setCurrentUser();
        }
        return self::$_instance;
    }

    private function setCurrentUser() {
        $session=self::getCookieConfig();
        session_name($session['session']);
        session_set_cookie_params($session['params']);
        $session_cookie=filter_input(INPUT_COOKIE, $session['session']);
        if ($session_cookie) {
            if (!session_start()) {
                throw new AppException('session_start failed.');
            }
            if (isset($_SESSION['user'])) {
                $this->user=$_SESSION['user'];
                session_commit();
                return;
            }
            $user=$this->refresh();
            if ($user) {
                $this->user=$user;
                $_SESSION['user']=$user;
                session_commit();
                return;
            }
            setcookie($session['session'], '', 1, $session['path'], $session['host'], false, true);
            session_destroy();
        } else {
            $user=$this->refresh();
            if ($user) {
                if (!session_start()) {
                    throw new AppException('session_start failed.');
                }
                $this->user=$user;
                $_SESSION['user']=$user;
                session_commit();
                return;
            }
        }
        $this->user=new User;
    }

    private function checkAccess(?array $scope): bool {
        $admins=getenv('SITE_ADMINS')?explode(',', getenv('SITE_ADMINS')):[];
        if (array_search($this->user->getLogin(), $admins)!==false) {
            return true;
        }
        if (is_null($scope)) {
            return !is_null($this->user->getLogin());
        }
        $user_scope=$this->user->getScope();
        foreach ($scope AS $item) {
            if (array_search($item, $user_scope)!==false) {
                return true;
            }
        }
        return false;
    }

    # Функции работы с долговременными сессиями
    public static function start(UserInterface $user) {
        $uid=self::genRandomString(32);
        $token=self::genRandomString(32);
        self::setUidCookie($uid);
        self::setTokenCookie($token);
        self::dbInsertSession($uid, $token, $user);
        return;
    }

    public function refresh(): ?UserInterface {
        $uid=self::getUidCookie();
        if(!$uid) {
            return null;
        }
        $session=self::dbSelectSession($uid);
        if(!$session) {
            return null;
        }
        $token=self::getTokenCookie();
        if($session->token!=$token) {
            self::dbDeleteSession($uid);
            self::dropCookie();
            return null;
        }
        $user=unserialize($session->user_entity);
        if(!$user->stillActive()) {
            self::dbDeleteSession($uid);
            self::dropCookie();
            return null;
        }
        $new_token=self::genRandomString(32);
        self::dbUpdateSession($uid, $new_token, $user);
        self::setUidCookie($uid);
        self::setTokenCookie($new_token);
        return $user;
    }

    public static function destroy(): bool {
        $uid=self::getUidCookie();
        if(!$uid) {
            return false;
        }
        self::dbDeleteSession($uid);
        self::dropCookie();
        return true;
    }

    private static function genRandomString($length): string {
        $symbols='abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ01234567890';
        $max_index=strlen($symbols)-1;
        $string='';
        for ($i=0; $i<$length; $i++) {
            $string.=$symbols[rand(0, $max_index)];
        }
        return $string;
    }

    private static function dbInsertSession(string $uid, string $token, UserInterface $user) {
        $session=self::getCookieConfig();
        $s=DB::prepare('INSERT INTO auth_sessions (uid, token, user_entity, expires, ip, browser) VALUES (?,?,?,?,?,?)');
        $s->execute([$uid, $token, serialize($user), date('c',time()+$session['time']), getenv('REMOTE_ADDR'), getenv('HTTP_USER_AGENT')]);
    }

    private static function dbUpdateSession(string $uid, string $new_token, UserInterface $user) {
        $session=self::getCookieConfig();
        $s=DB::prepare('UPDATE auth_sessions SET token=?, user_entity=?, expires=?, ip=?, browser=? WHERE uid=?');
        $s->execute([$new_token, serialize($user), date('c',time()+$session['time']), getenv('REMOTE_ADDR'), getenv('HTTP_USER_AGENT'), $uid]);
    }

    private static function dbSelectSession($uid) {
        $s=DB::prepare('SELECT * FROM auth_sessions WHERE uid=? AND expires>NOW()');
        $s->execute([$uid]);
        return $s->fetch(PDO::FETCH_OBJ);
    }

    private static function dbDeleteSession($uid) {
        $s=DB::prepare('DELETE FROM auth_sessions WHERE uid=?');
        $s->execute([$uid]);
    }

    private static function dbDeleteExpiredSession() {
        DB::query('DELETE FROM auth_sessions WHERE expires<NOW()');
    }

    private static function setUidCookie(string $uid): void {
        $cookie=self::getCookieConfig();
        $params=$cookie['params'];
        $params['expires']=time()+$cookie['time'];
        setcookie($cookie['uid'], $uid, $params);
    }

    private static function setTokenCookie(string $token): void {
        $cookie=self::getCookieConfig();
        $params=$cookie['params'];
        $params['expires']=time()+$cookie['time'];
        setcookie($cookie['token'], $token, $params);
    }

    private static function getUidCookie(): ?string {
        $cookie=self::getCookieConfig();
        return filter_input(INPUT_COOKIE, $cookie['uid']);
    }

    private static function getTokenCookie(): ?string {
        $cookie=self::getCookieConfig();
        return filter_input(INPUT_COOKIE, $cookie['token']);
    }

    private static function dropCookie(): void {
        $cookie=self::getCookieConfig();
        $params=$cookie['params'];
        $params['lifetime']=1;
        setcookie($cookie['uid'], '', $params);
        setcookie($cookie['token'], '', $params);
    }

    private static function getCookieConfig(): array {
        if(isset(self::$cookie)) {
            return self::$cookie;
        }
        $name=getenv('COOKIE_NAME')?getenv('COOKIE_NAME'):'neuron';
        self::$cookie=['session'=>$name.'-session', 'uid'=>$name.'-uid', 'token'=>$name.'-token', 'time'=>getenv('COOKIE_TIME')?getenv('COOKIE_TIME'):2592000, 'params'=>['path'=>getenv('COOKIE_PATH')?getenv('COOKIE_PATH'):'/', 'domain'=>getenv('COOKIE_DOMAIN')?getenv('COOKIE_DOMAIN'):'', 'secure'=>false, 'httponly'=>true, 'samesite'=>'Strict']];
        return self::$cookie;
    }

}
