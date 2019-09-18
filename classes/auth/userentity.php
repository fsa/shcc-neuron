<?php

namespace Auth;

class UserEntity implements UserInterface {
    
    use jsonUnserialize;

    public $id;
    public $login='guest';
    public $name='Гость';
    public $email;
    public $scope='["guest"]';
    public $disabled=false;

    public function __sleep() {
        return ['id','login','name','email','scope','disabled'];
    }
    
    public function jsonSerialize() {
        return [
            'id'=>$this->id,
            'login'=>$this->login,
            'name'=>$this->name,
            'email'=>$this->email,
            'scope'=>$this->scope,
            'disabled'=>$this->disabled
        ];
    }

    public function getId(): ?int {
        return $this->id;
    }
    
    public function getLogin(): string {
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

}
