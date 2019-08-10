<?php

namespace Auth;

use DB,
    PDO;

class User {

    private $user;

    public static function authenticate($login, $password): UserInterface {
        $user=new self;
        $user->fetch($login);
        if ($user->userExist() and $user->isPasswordCorrect($password)) {
            return $user->user;
        }
        throw new \AppException('Неверное имя пользователя или пароль!');
    }

    public static function authenticateExternal(UserInterface $user_ext) {
        $user=new self;
        $user->fetchExt($user_ext->getLogin());
        return $user->user;
    }

    public static function checkScope($scope, $user_id) {
        #TODO: проверить scope
        return $scope;
    }

    public function fetch($login) {
        $st=DB::prepare('SELECT * FROM auth_users WHERE login=? AND disabled=false');
        $st->execute([$login]);
        $st->setFetchMode(PDO::FETCH_CLASS, UserEntity::class);
        $this->user=$st->fetch();
    }

    public function fetchExt($login) {
        $st=DB::prepare('SELECT u.* FROM auth_users u LEFT JOIN user_ext e ON u.id=e.user_id WHERE e.login=? AND u.disabled=0');
        $st->execute([$login]);
        $st->setFetchMode(PDO::FETCH_CLASS, UserEntity::class);
        $this->user=$st->fetch();
    }

    public function userExist() {
        return $this->user instanceof UserEntity;
    }

    public function isPasswordCorrect($password) {
        return password_verify($password, $this->user->password);
    }

}
