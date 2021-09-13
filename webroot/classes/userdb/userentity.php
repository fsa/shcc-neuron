<?php

namespace UserDB;

class UserEntity extends \Entity {

    const TABLENAME='users';
    const ID='uuid';

    public $uuid;
    public $login;
    public $password_hash;
    public $name;
    public $email;
    public $scope;
    public $groups;
    public $disabled;

    public $password;

    protected function getColumnValues(): array {
        $row=get_object_vars($this);
        unset($row['password']);
        if($this->password) {
            $row['password_hash']=password_hash($this->password, PASSWORD_DEFAULT, ['cost'=>12]);
        } else {
            unset($row['password_hash']);
        }
        if(is_array($this->scope)) {
            $row['scope']='{'.join(',', $row['scope']).'}';
        }
        if(is_array($this->groups)) {
            $row['groups']='{'.join(',', $row['groups']).'}';
        }
        $row['disabled']=$this->disabled===true?'t':'f';
        return $row;
    }

    public function memberOfScope($scope) {
        return array_search($scope, explode(',',trim($this->scope, '{}')))!==false;
    }

    public function memberOfGroup($group) {
        return array_search($group, explode(',',trim($this->groups, '{}')))!==false;
    }

}
