<?php

namespace Auth;

use DB;

class UserEntity extends \Entity {

    const TABLENAME='auth_users';

    public $id;
    public $login;
    public $password;
    public $name;
    public $email;
    public $scope;
    public $ip_register;
    public $registered;
    public $activate_key;
    public $activated;
    public $updated;
    public $disabled;

    public function __construct() {
        $this->password=null;
        if(!is_null($this->scope)) {
            $this->scope=json_decode($this->scope);
        }
    }

    public function memberOf($group) {
        if (is_null($this->scope)) {
            return false;
        }
        return array_search($group, $this->scope)!==false;
    }

    public function insert() {
        $this->id=DB::insert(self::TABLENAME, [
            "login"=>$this->login,
            "password"=>password_hash($this->password, PASSWORD_DEFAULT),
            "name"=>$this->name,
            "email"=>$this->email,
            "scope"=>is_null($this->scope)?'[]':json_encode($this->scope),
        ]);
        return $this->id;
    }

    public function update() {
        $this->updated=date('c');
        $user=[
            "id"=>$this->id,
            "login"=>$this->login,
            "name"=>$this->name,
            "email"=>$this->email,
            "scope"=>is_null($this->scope)?'[]':json_encode($this->scope),
            "updated"=>$this->updated,
            "disabled"=>$this->disabled?'t':'f'
        ];
        if(!is_null($this->password)) {
            $user['password']=password_hash($this->password, PASSWORD_DEFAULT);
        }
        return DB::update(self::TABLENAME, $user);
    }

}