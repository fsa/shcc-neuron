<?php

namespace Yandex\SmartHome\Capabilities;

abstract class State implements \JsonSerializable {

    public $type;
    public $state;
    public $action_result;

    public function jsonSerialize() {
        $result=[
            'type'=>$this->type,
            'state'=>$this->state
        ];
        if(!is_null($this->action_result)) {
            $result['action_result']=$this->action_result;
        }
        return $result;
    }
    
    public function setActionResultDone() {
        $this->action_result=['status'=>'DONE'];
    }
    
    public function setActionResultError(?string $code=null, ?string $message=null) {
        $result=['status'=>'ERROR'];
        if(!is_null($code)) {
            $result['error_code']=$code;
        }
        if(!is_null($message)) {
            $result['error_message']=$message;
        }
        $this->action_result=$result;
    }

}
