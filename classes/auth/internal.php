<?php

namespace Auth;

use HTML,
    AppException;

class Internal {

    private static $_instance;
    private $user;

    public static function grantAccess(array $scope=[]): void {
        $auth=self::getInstance();
        if (!$auth->memberOf($scope)) {
            $user=$auth->getUser();
            if ($user->login='guest') {
                HTML::showLoginForm(getenv('REQUEST_URI'));
                exit;
            }
            throw new AppException('Доступ пользователю запрещён');
        }
    }

    public static function memberOf(array $scope=[]): bool {
        $auth=self::getInstance();
        return $auth->checkAccess($scope);
    }

    public static function login(UserInterface $user): void {
        self::$_instance=new self($user);
    }

    public static function logout(): void {
        $session=\Settings::get('session');
        session_name($session->name);
        session_set_cookie_params(0, $session->path, getenv('HTTP_HOST'), false, true);
        session_start();
        unset($_SESSION['user']);
        Session::destroy();
        session_commit();
    }

    public static function getUser(): UserInterface {
        return self::getInstance()->user;
    }

    private static function getInstance(): self {
        if (is_null(self::$_instance)) {
            self::$_instance=new self;
        }
        return self::$_instance;
    }

    private function __construct($user=false) {
        $session=\Settings::get('session');
        session_name($session->name);
        session_set_cookie_params(0, $session->path, getenv('HTTP_HOST'), false, true);
        session_start();
        if ($user) {
            $this->user=$user;
            $_SESSION['user']=$user;
            Session::start($user);
            session_commit();
            return;
        }
        if (!isset($_SESSION['user'])) {
            $user=Session::refresh();
            if (!$user) {
                session_destroy();
                $this->user=new UserEntity;
                return;
            }
            $this->user=$user;
            $_SESSION['user']=$user;
            session_commit();
            return;
        }
        $this->user=$_SESSION['user'];
        session_commit();
    }

    private function checkAccess(array $scope): bool {
        $admins=\Settings::get('admins');
        if (array_search($this->user->getId(), $admins)!==false) {
            return true;
        }
        if (sizeof($scope)==0) {
            return $this->user->getLogin()!='guest';
        }
        $user_scope=$this->user->getScope();
        foreach ($scope AS $item) {
            if (array_search($item, $user_scope)!==false) {
                return true;
            }
        }
        return false;
    }

}
