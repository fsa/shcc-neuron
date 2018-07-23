<?php

class User implements UserInterface {

    public $id=0;
    public $login='guest';
    public $name='Гость';
    public $email;
    #date_register
    #updated
    public $groups='["guest"]';
    public $disabled=0;

    public function getLogin() {
        return $this->login;
    }

    public function getName() {
        return $this->name;
    }

    public function getEmail() {
        return $this->email;
    }

    public function getGroups() {
        return json_decode($this->groups);
    }

}
