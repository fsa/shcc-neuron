<?php

namespace Yandex\SmartHome\Capabilities;

class OnOffResult extends Result {

    public $type="devices.capabilities.on_off";
    
    public function __construct(?string $error_code=null, ?string $error_message=null) {
        if(is_null($error_code)) {
            $this->state=[
                "instance"=>'on',
                "action_result"=> [
                    "status"=> "DONE"
                ]
            ];
        } else {
            $this->state=[
                "instance"=>'on',
                "action_result"=> [
                    "status"=> "ERROR",
                    "error_code"=>$error_code,
                    "error_message"=>$error_message
                ]
            ];
        }
    }
    
}
