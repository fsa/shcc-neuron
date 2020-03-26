<?php

namespace Auth;

use DB,
    PDO,
    Settings,
    httpResponse;

class Session {

    private static $_instance;
    private $user;

    public static function grantAccess(array $scope=null): void {
        $auth=self::getInstance();
        if ($auth->checkAccess($scope)) {
            return;
        }
        httpResponse::showAccessError(!is_null($auth->user->getLogin()));
        die;
    }

    public static function memberOf(array $scope=null): bool {
        $auth=self::getInstance();
        return $auth->checkAccess($scope);
    }

    public static function login(UserInterface $user): void {
        $session=Settings::get('session');
        session_name($session['name']);
        session_set_cookie_params(0, $session['path'], getenv('HTTP_HOST'), false, true);
        self::$_instance=new self;
        self::$_instance->user=$user;
        session_start();
        $_SESSION['user']=$user;
        session_commit();
        self::start($user);
    }

    public static function logout(): void {
        self::destroy();
        $session=Settings::get('session');
        session_name($session['name']);
        session_set_cookie_params(0, $session['path'], getenv('HTTP_HOST'), false, true);
        session_start();
        $_SESSION=[];
        session_destroy();
        setcookie($session['name'], '', time()-42000, $session['path'], getenv('HTTP_HOST'), false, true);
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
        $session=Settings::get('session');
        session_name($session['name']);
        session_set_cookie_params(0, $session['path'], getenv('HTTP_HOST'), false, true);
        $session_cookie=filter_input(INPUT_COOKIE, $session['name']);
        if ($session_cookie) {
            session_start();
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
            setcookie($session['name'], '', time()-42000, $session['path'], getenv('HTTP_HOST'), false, true);
            session_destroy();
        } else {
            $user=$this->refresh();
            if ($user) {
                session_start();
                $this->user=$user;
                $_SESSION['user']=$user;
                session_commit();
                return;
            }
        }
        $this->user=new User;
    }

    private function checkAccess(?array $scope): bool {
        $admins=Settings::get('admins', []);
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
        $session=Settings::get('session');
        $s=DB::prepare('INSERT INTO auth_sessions (uid, token, user_entity, expires, ip, browser) VALUES (?,?,?,?,?,?)');
        $s->execute([$uid, $token, serialize($user), date('c',time()+$session['time']), getenv('REMOTE_ADDR'), getenv('HTTP_USER_AGENT')]);
    }
    
    private static function dbUpdateSession(string $uid, string $new_token, UserInterface $user) {
        $session=Settings::get('session');
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
        $cookie=Settings::get('session');
        setcookie($cookie['uid'], $uid, time()+$cookie['time'], $cookie['path'], getenv('HTTP_HOST'), false, true);
    }
    
    private static function setTokenCookie(string $token): void {
        $cookie=Settings::get('session');
        setcookie($cookie['token'], $token, time()+$cookie['time'], $cookie['path'], getenv('HTTP_HOST'), false, true);
    }
    
    private static function getUidCookie(): ?string {
        $cookie=Settings::get('session');
        return filter_input(INPUT_COOKIE, $cookie['uid']);
    }

    private static function getTokenCookie(): ?string {
        $cookie=Settings::get('session');
        return filter_input(INPUT_COOKIE, $cookie['token']);
    }

    private static function dropCookie(): void {
        $cookie=Settings::get('session');
        setcookie($cookie['uid'],'',time()-3600, $cookie['path'], getenv('HTTP_HOST'), false, true);
        setcookie($cookie['token'],'',time()-3600, $cookie['path'], getenv('HTTP_HOST'), false, true);
    }

}
