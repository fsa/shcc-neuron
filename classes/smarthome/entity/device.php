<?php

/**
 * SHCC 0.7.0-dev
 * 2020-11-25
 */

namespace SmartHome\Entity;

class Device {

    public $uid;
    public $hwid;
    public $description;
    public $entity;
    public $place_id;

    public function setInitData($data) {
        $this->entity=json_encode($data);
    }

    public function getInitData() {
        return json_decode($this->entity, true);
    }

}
