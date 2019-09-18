<?php

namespace Yandex\SmartHome\Capabilities;

abstract class Result {

    public $type;
    public $state;

    public function __construct(string $instance, ?string $error_code=null, ?string $error_message=null) {
        if (is_null($error_code)) {
            $this->state=[
                "instance"=>$instance,
                "action_result"=>[
                    "status"=>"DONE"
                ]
            ];
        } else {
            $this->state=[
                "instance"=>$instance,
                "action_result"=>[
                    "status"=>"ERROR",
                    "error_code"=>$error_code,
                    "error_message"=>$error_message
                ]
            ];
        }
    }

}
