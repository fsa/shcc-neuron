<?php

namespace Yandex\SmartHome\Capabilities;

class ColorModeResult extends State {

    public $type="devices.capabilities.color_setting";

    public function __construct(?string $error_code=null, ?string $error_message=null) {
        parent::__construct('temperature_k', $error_code, $error_message);
    }

}
