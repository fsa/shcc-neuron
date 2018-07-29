<?php

namespace Auth;

use DB,
    PDO;

class User {

    private $user;

    public static function authenticate($login,$password): UserInterface {
        $user=new self;
        $user->fetch($login);
        if($user->userExist() and $user->isPasswordCorrect($password)) {
            return $user->user;
        }
        throw new \AppException('Неверное имя пользователя или пароль!');
    }

    public function fetch($login) {
        $st=DB::prepare('SELECT * FROM users WHERE login=? AND disabled=0');
        $st->execute([$login]);
        $st->setFetchMode(PDO::FETCH_CLASS,UserEntity::class);
        $this->user=$st->fetch();
    }

    public function userExist() {
        return $this->user instanceof UserEntity;
    }
    
    public function isPasswordCorrect($password) {
        return password_verify($password,$this->user->password);
    }
    
}
