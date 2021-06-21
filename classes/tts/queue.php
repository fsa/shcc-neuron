<?php

namespace Tts;

class Queue {

    const PROJ='a';
    const CHMOD=0600;
    const MAX_MESSAGE_SIZE=1024;

    private $queue;

    public function __construct() {
        if(msg_queue_exists(ftok(__FILE__,self::PROJ))) {
            $this->getQueue();
        }
    }

    public function getQueue() {
        $this->queue=msg_get_queue(ftok(__FILE__,self::PROJ),self::CHMOD);
    }

    public function dropQueue() {
        if(is_null($this->queue)) {
            return;
        }
        if(msg_remove_queue($this->queue)) {
            $this->queue=null;
        }
    }

    public function addMessage($text) {
        if(is_null($this->queue)) {
            return false;
        }
        $queue_stat=msg_stat_queue($this->queue);
        # Защита от переполнения сообщений при падении сервиса TTS
        if($queue_stat['msg_qnum']>15) {
            syslog(LOG_WARNING, 'TTS Drop message: '.$text);
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
