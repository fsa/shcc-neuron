<?php

namespace Yandex\SmartHome\Capabilities;

abstract class Description implements \JsonSerializable {

    public $type;
    public $retrievable;

    public function __construct(?bool $state=null) {
        $this->retrievable=$state;        ;
    }

    public function jsonSerialize() {
        $result=[
            'type'=>$this->type
        ];
        if(!is_null($this->retrievable)) {
            $result['retrievable']=$this->retrievable;
        }
        return $result;
    }

}
