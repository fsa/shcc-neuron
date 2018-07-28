<?php

class Auth {

    private static $_instance;
    private $user;

    public static function grantAccess(array $groups=[]) {
        if (!self::memberOf($groups)) {
            throw new AppException('Доступ пользователю запрещён');
        }
    }

    public static function memberOf(array $groups=[]): bool {
        $auth=self::getInstance();
        return $auth->checkAccess($groups);
    }

    public static function login(Auth\UserInterface $user) {
        self::$_instance=new self($user);
    }

    public static function logout() {
        $session=Settings::get('session');
        session_name($session->name);
        session_start();
        unset($_SESSION['user']);
        session_commit();
    }
    
    public static function getUser(): Auth\UserInterface {
        return self::getInstance()->user;
    }

    private static function getInstance(): self {
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
            $this->user=new Auth\UserEntity;
            return;
        }
        $this->user=$_SESSION['user'];
        session_commit();
    }

    private function checkAccess(array $groups): bool {
        $admins=\Settings::get('admins');
        if(array_search($this->user->getId(),$admins)!==false) {
            return true;
        }
        if(sizeof($groups)==0) {
            return $this->user->getLogin()!='guest';
        }
        $user_groups=$this->user->getGroups();
        foreach ($groups AS $group) {
            if (array_search($group,$user_groups)!==false) {
                return true;
            }
        }
        return false;
    }

}
