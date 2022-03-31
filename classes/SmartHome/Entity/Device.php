<?php

namespace SmartHome\Entity;

class Device {

    public $uid;
    public $hwid;
    public $description;
    public $entity;

    public function setInitData($data) {
        $this->entity=json_encode($data);
    }

    public function getInitData() {
        return json_decode($this->entity, true);
    }

}
