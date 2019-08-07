<?php

namespace Auth;

class UserEntity implements UserInterface {

    public $id;
    public $login='guest';
    public $name='Гость';
    public $email;
    public $groups='["guest"]';
    public $disabled=false;

    public function __sleep() {
        return ['id', 'login', 'name', 'email', 'groups', 'disabled'];
    }

    public function getId() {
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

    public function getGroups(): array {
        return json_decode($this->groups);
    }

}
