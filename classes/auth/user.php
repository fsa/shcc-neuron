<?php

namespace Auth;

use DB,
    PDO;

class User implements UserInterface {

    public $id;
    public $login;
    public $name='Гость';
    public $email;
    public $scope='["guest"]';
    public $disabled=false;

    public function __sleep() {
        return ['id','login','name','email','scope','disabled'];
    }

    public function getId(): ?int {
        return $this->id;
    }

    public function getLogin(): ?string {
        return $this->login;
    }

    public function getName(): string {
        return $this->name;
    }

    public function getEmail(): string {
        return $this->email;
    }

    public function getScope(): array {
        return json_decode($this->scope);
    }

    public function stillActive(): bool {
        $st=DB::prepare('SELECT id, login, name, email, scope, disabled FROM auth_users WHERE id=?');
        $st->execute([$this->id]);
        $user=$st->fetch(PDO::FETCH_OBJ);
        $this->id=$user->id;
        $this->login=$user->login;
        $this->name=$user->name;
        $this->email=$user->email;
        $this->scope=$user->scope;
        $this->disabled=$user->disabled=='t'?true:false;
        return !$this->disabled;
    }

    private function isPasswordCorrect(string $password) {
        return password_verify($password, $this->password);
    }

    public static function authenticate(string $login, string $password): ?UserInterface {
        $user=self::fetch($login);
        if ($user and $user->isPasswordCorrect($password)) {
            return $user;
        }
        return null;
    }

    public static function checkScope($scope, $user_id) {
        #TODO: проверить scope
        return $scope;
    }

    public static function fetch(string $login) {
        $st=DB::prepare('SELECT id, login, password, name, email, scope, disabled FROM auth_users WHERE login=? AND disabled=false');
        $st->execute([$login]);
        $st->setFetchMode(PDO::FETCH_CLASS, self::class);
        return $st->fetch();
    }

}
