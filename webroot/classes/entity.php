<?php

abstract class Entity {

    const ID='id';

    public function update() {
        $class=get_called_class();
        $id=$class::ID;
        return DB::update($class::TABLENAME, $this->getColumnValues(), $id);
    }

    public function insert() {
        $class=get_called_class();
        $values=$this->getColumnValues();
        $id=$class::ID;
        unset($values[$id]);
        $this->$id=DB::insert($class::TABLENAME, $values, $id);
        return $this->$id;
    }

    public function upsert() {
        $class=get_called_class();
        if (is_null($this->{$class::ID})) {
            $this->insert();
        } else {
            $this->update();
        }
    }

    protected function getColumnValues(): array {
        return get_object_vars($this);
    }

    public function inputPostInteger($param) {
        $this->$param=filter_input(INPUT_POST, $param, FILTER_VALIDATE_INT);
    }

    public function inputPostString($param) {
        $this->$param=filter_input(INPUT_POST, $param);
    }

    public function inputPostTextarea($param) {
        $this->$param=filter_input(INPUT_POST, $param);
    }

    public function inputPostDate($param) {
        $this->$param=filter_input(INPUT_POST, $param);
        if(!$this->$param) {
            $this->$param=null;
        }
    }

    public function inputPostDatetime($param) {
        $this->$param=filter_input(INPUT_POST, $param);
    }

    public function inputPostCheckbox($param) {
        $this->$param=filter_input(INPUT_POST, $param)=='on';
    }


    public function inputPostChecboxArray($param) {
        $this->$param=array_keys(filter_input(INPUT_POST, $param, FILTER_DEFAULT, FILTER_REQUIRE_ARRAY));
    }

    public static function getEntity($param, $method=INPUT_POST): self {
        $id=filter_input($method, $param);
        $class=get_called_class();
        return $id?$class::fetch($id):new $class;
    }

    public static function fetch($id): ?self {
        $class=get_called_class();
        $s=DB::prepare('SELECT * FROM '.$class::TABLENAME.' WHERE '.$class::ID.'=?');
        $s->execute([$id]);
        $s->setFetchMode(PDO::FETCH_CLASS, $class);
        $result=$s->fetch();
        return $result?$result:null;
    }

}
