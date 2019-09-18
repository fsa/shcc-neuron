<?php

namespace Yandex\SmartHome\Capabilities;

class OnOffResult extends Result {

    public $type="devices.capabilities.on_off";
    
    public function __construct(?string $error_code=null, ?string $error_message=null) {
        parent::__construct('on', $error_code, $error_message);
    }

}
