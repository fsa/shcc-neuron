<?php

class Auth {

    private static $_instance;
    private $user;

    public static function grantAccess(array $groups) {
        if (!self::memberOf($groups)) {
            throw new AppException('Доступ пользователю запрещён');
        }
    }

    public static function memberOf(array $groups) {
        $auth=self::getInstance();
        return $auth->checkAccess($groups);
    }

    public static function login($user) {
        self::$_instance=new self($user);
    }

    public static function logout() {
        $session=Settings::get('session');
        session_name($session->name);
        session_start();
        unset($_SESSION['user']);
        session_commit();
    }
    
    public static function getUser() {
        return self::$_instance->user;
    }

    private static function getInstance() {
        if (is_null(self::$_instance)) {
            self::$_instance=new self;
        }
        return self::$_instance;
    }

    private function __construct($user=false) {
        $session=Settings::get('session');
        session_name($session->name);
        session_start();
        if ($user) {
            $this->user=$user;
            $_SESSION['user']=$user;
            session_commit();
            return;
        }
        if (!isset($_SESSION['user'])) {
            session_destroy();
            $this->user=new User;
            return;
        }
        $this->user=$_SESSION['user'];
        session_commit();
    }

    private function checkAccess(array $groups) {
        return true;
        #TODO проверить на админа
        foreach ($groups AS $group) {
            if (isset($this->user->$group) and $this->user->$group==1) {
                return true;
            }
        }
        return false;
    }

}
