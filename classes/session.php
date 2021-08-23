<?php

class Session {

    private static $_session;
    private $cookie_session;
    private $cookie_uid;
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
            throw new AuthException();
        }
        throw new AccessException();
    }

    public static function memberOf(array $scope=null): bool {
        $session=self::getInstance();
        return $session->checkAccess($scope);
    }

    public static function getUserId() {
        $session=self::getInstance();
        if(isset($session->user)) {
            return $session->user->getUserId();
        }
        return null;
    }

    public static function getUser() {
        return (self::getInstance())->user;
    }

    private function __construct() {
        $name=getenv('SESSION_NAME')?getenv('SESSION_NAME'):'neuron';
        $this->cookie_session=$name.'_session';
        $this->cookie_uid=$name.'_uid';
        $this->cookie_token=$name.'_token';
        $this->cookie_time=getenv('SESSION_TIME')?getenv('SESSION_TIME'):2592000;
        $this->cookie_params=[
            'path'=>getenv('SESSION_PATH')?getenv('SESSION_PATH'):'/',
            'domain'=>getenv('SESSION_DOMAIN')?getenv('SESSION_DOMAIN'):'',
            'secure'=>getenv('SESSION_SECURE')?getenv('SESSION_SECURE'):false,
            'httponly'=>true,
            'samesite'=>'Strict'
        ];
        switch (getenv('SESSION_STORAGE')) {
            case 'pdo':
                $this->session_storage=$this->getSessionStoragePDO();
                break;
            default:
                $this->session_storage=$this->getSessionStorageRedis($name);
        }
        session_name($this->cookie_session);
        session_set_cookie_params($this->cookie_params);
        $session_cookie=filter_input(INPUT_COOKIE, $this->cookie_session);
        syslog(LOG_DEBUG, 'Start -> Начало поиска сессии. ');
        if ($session_cookie) {
            syslog(LOG_DEBUG, 'Попытка восстановления PHP сессии из cookie '.$session_cookie);
            $this->phpSessionStart();
            if ($this->getPhpSessionUser()) {
                session_commit();
                syslog(LOG_DEBUG, 'Finish -> Использован пользователь из PHP сессии.');
                return;
            }
            if ($this->restorePhpSession()) {
                $this->setPhpSessionUser();
                session_commit();
                syslog(LOG_DEBUG, 'Finish -> Восстановлен пользователь из сессии в БД (PHP сессия не сработала).');
                return;
            }
            $this->dropPhpSessionCookie();
            $this->dropLongSessionCookie();
            unset($_SESSION['user']);
            session_commit();
            syslog(LOG_DEBUG, 'Finish -> Пользователь не опознан, сессии сброшены.');
            return;
        }
        if ($this->restorePhpSession()) {
            $this->phpSessionStart();
            $this->setPhpSessionUser();
            $_SESSION['drop_token']=filter_input(INPUT_COOKIE, $this->cookie_uid);
            session_commit();
            syslog(LOG_DEBUG, 'Finish -> Восстановлен пользователь из сессии в БД.');
            return;
        }
    }

    private function getPhpSessionUser(): bool {
        if (isset($_SESSION['user'])) {
            $this->user=$_SESSION['user'];
            return true;
        }
        syslog(LOG_DEBUG, 'При восстановлении PHP сессии в ней не найден пользователь.');
        return false;
    }

    private function setPhpSessionUser() {
        $_SESSION['user']=$this->user;
    }

    private function restorePhpSession() {
        syslog(LOG_DEBUG, 'Восстановление пользователя из сессии в БД.');
        $uid=filter_input(INPUT_COOKIE, $this->cookie_uid);
        if (!$uid) {
            syslog(LOG_DEBUG, 'Отсутствует UID пользователя в cookie.');
            return false;
        }
        syslog(LOG_DEBUG, 'Найден UID сессии в cookie.');
        $db_session=$this->session_storage->select($uid);
        if (!$db_session) {
            syslog(LOG_DEBUG, 'UID сессии не найден в БД, сбрасываем сессию в cookie.');
            $this->dropLongSessionCookie();
            return false;
        }
        $token=filter_input(INPUT_COOKIE, $this->cookie_token);
        if ($db_session->token!=$token) {
            syslog(LOG_DEBUG, 'Токен сессии не соответствует, сбрасываем сессию в cookie, удаляем сессию из БД.');
            $this->session_storage->delete($uid);
            $this->dropLongSessionCookie();
            return false;
        }
        $class_name=$db_session->class;
        $user=new $class_name(...$db_session->args);
        if (!$user->validate()) {
            syslog(LOG_DEBUG, 'Пользователь не прошёл валидацию.');
            $this->session_storage->delete($uid);
            $this->dropLongSessionCookie();
            return false;
        }
        $this->user=$user;
        $new_token=$this->generateRandomString();
        $this->session_storage->update($uid, ['token'=>$new_token, 'class'=>get_class($this->user), 'args'=>$this->user->getConstructorArgs()], $this->cookie_time);
        $this->setCookie($uid, $new_token);
        syslog(LOG_DEBUG, 'Пользователь восстановлен. Присвоен новый токен для сессии '.$uid);
        return true;
    }

    private function login($user) {
        $old_uid=filter_input(INPUT_COOKIE, $this->cookie_uid);
        if ($old_uid) {
            $this->session_storage->delete($old_uid);
        }
        $this->phpSessionStart();
        $this->user=$user;
        $_SESSION['user']=$user;
        session_commit();
        $uid=$this->generateUuid();
        $token=$this->generateRandomString();
        $this->session_storage->insert($uid, ['token'=>$token, 'class'=>get_class($this->user), 'args'=>$this->user->getConstructorArgs()], $this->cookie_time);
        $this->setCookie($uid, $token);
    }

    private function logout() {
        $this->user=null;
        $session_cookie=filter_input(INPUT_COOKIE, $this->cookie_session);
        if ($session_cookie) {
            $this->phpSessionStart();
            unset($_SESSION['user']);
            session_commit();
        }
        $this->dropPhpSessionCookie();
        $uid=filter_input(INPUT_COOKIE, $this->cookie_uid);
        if ($uid) {
            $this->session_storage->delete($uid);
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

    private function generateUuid() {
        return sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),
            mt_rand( 0, 0xffff ),
            mt_rand( 0, 0x0fff ) | 0x4000,
            mt_rand( 0, 0x3fff ) | 0x8000,
            mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff )
        );
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

    private function setCookie(string $uid, string $token): void {
        $params=$this->cookie_params;
        $params['expires']=time()+$this->cookie_time;
        setcookie($this->cookie_uid, $uid, $params);
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
        setcookie($this->cookie_uid, '', $params);
        setcookie($this->cookie_token, '', $params);
    }

    private function dropPhpSessionCookie(): void {
        $params=$this->cookie_params;
        $params['expires']=1;
        setcookie($this->cookie_session, '', $params);
    }

    private function getSessionStoragePDO() {
        return new class {
            public function insert(string $uid, $data, int $session_time) {
                $s=DB::prepare('INSERT INTO sessions (uid, entity, expires) VALUES (?,?,?)');
                $s->execute([$uid, $data, date('c', time()+$session_time)]);
            }

            public function update(string $uid, $data, int $session_time) {
                $s=DB::prepare('UPDATE sessions SET entity=?, expires=? WHERE uid=?');
                $s->execute([$data, date('c', time()+$session_time), $uid]);
            }

            public function select($uid) {
                $s=DB::prepare('SELECT entity FROM sessions WHERE uid=? AND expires>NOW()');
                $s->execute([$uid]);
                $entity=$s->fetchColumn();
                return json_decode($entity);
            }

            public function delete($uid) {
                $s=DB::prepare('DELETE FROM sessions WHERE uid=? OR expires<NOW()');
                $s->execute([$uid]);
            }
        };
    }

    private function getSessionStorageRedis($name) {
        return new class ($name) {
            private $name;

            public function __construct($name) {
                $this->name=$name;
            }

            public function insert(string $uid, $data, int $session_time) {
                DBRedis::setEx($this->name.'_session:'.$uid, $session_time, json_encode($data));
            }

            public function update(string $uid, $data, int $session_time) {
                DBRedis::setEx($this->name.'_session:'.$uid, $session_time, json_encode($data));
            }

            public function select($uid) {
                $session=DBRedis::get($this->name.'_session:'.$uid);
                if(!$session) {
                    return null;
                }
                return json_decode($session);
            }

            public function delete($uid) {
                DBRedis::del($this->name.'_session:'.$uid);
            }
        };
    }

}
