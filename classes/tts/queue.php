<?php

namespace Tts;

class Queue {

    const PROJ='a';
    const CHMOD=0600;
    const MAX_MESSAGE_SIZE=512;

    private $queue;

    public function __construct() {
        $this->queue=msg_get_queue(ftok(__FILE__,self::PROJ),self::CHMOD);
    }
    
    public function addMessage($text) {
        $res=msg_send($this->queue, 1, $text, false);
        if($res===false) {
            throw new Exception('msg_send error');
        }
    }
    
    public function receiveMessage() {
        $res=msg_receive($this->queue,1,$msgtype,self::MAX_MESSAGE_SIZE,$message,false);
        if($res===false) {
            throw new Exception('msg_receive error');
        }
        return $message;
    }
}
