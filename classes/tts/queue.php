<?php

namespace Tts;

class Queue {

    const PROJ='a';
    const CHMOD=0600;
    const MAX_MESSAGE_SIZE=1024;

    private $queue;

    public function __construct() {
        $this->queue=msg_get_queue(ftok(__FILE__,self::PROJ),self::CHMOD);
    }
    
    public function addMessage($text) {
        $queue_stat=msg_stat_queue($this->queue);
        if($queue_stat['msg_qnum']>15 and time()-$queue_stat['msg_rtime']>30) {
            return false;
        }
        return msg_send($this->queue, 1, $text, false);
    }
    
    public function receiveMessage() {
        $res=msg_receive($this->queue,1,$msgtype,self::MAX_MESSAGE_SIZE,$message,false);
        if($res===false) {
            return false;
        }
        return $message;
    }
    
    public function dropOldMessage() {
        $queue_stat=msg_stat_queue($this->queue);
        if(time()-$queue_stat['msg_stime']>180) {
            while($this->receiveMessage());
        }
    }
}
