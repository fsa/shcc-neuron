<?php

namespace Yandex\SmartHome\Capabilities;

abstract class Description {

    public $type;
    public $retrievable;

    public function __construct(?bool $state=true) {
        $this->retrievable=$state;        ;
    }

}
