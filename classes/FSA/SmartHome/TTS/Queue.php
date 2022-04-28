<?php

namespace FSA\SmartHome\TTS;

use App;

class Queue
{
    const QUEUE_SIZE = 10;
    const LOG_SIZE = 10;
    private $redis;
    private $queue_name;
    private $queue_size;
    private $mute_name;
    private $log_name;
    private $log_size;

    public function __construct($redis, $prefix, $queue_size = self::QUEUE_SIZE, $log_size = self::LOG_SIZE)
    {
        $this->redis = $redis;
        $this->queue_name = $prefix . ':Queue';
        $this->log_name = $prefix . ':Message';
        $this->mute_name = $prefix . ':Mute';
        $this->queue_size = $queue_size;
        $this->log_size = $log_size;
    }

    public function dropQueue()
    {
        $this->redis->del($this->queue_name);
    }

    public function addMessage($text)
    {
        $this->addLogMessage($text);
        if ($this->redis->exists($this->mute_name)) {
            return;
        }
        $this->redis->lPush($this->queue_name, json_encode(['ts' => time(), 'text' => $text]), JSON_UNESCAPED_UNICODE);
        if ($this->redis->lLen($this->queue_name) > $this->queue_size) {
            $msg = $this->redis->rPop($this->queue_name);
            syslog(LOG_NOTICE, __FILE__ . ':' . __LINE__ . ' TTS Drop queue message: ' . $msg[1]);
        }
    }

    public function waitMessage($timeout = 30)
    {
        return $this->redis->brPop($this->queue_name, $timeout);
    }

    public function addLogMessage($message)
    {
        $log=['message'=>$message, 'ts'=>time()];
        $this->redis->rPush($this->log_name, json_encode($log, JSON_UNESCAPED_UNICODE));
        if ($this->redis->lLen($this->log_name) > $this->log_size) {
            $this->redis->lPop($this->log_name);
        }
    }

    public function getLogMessages()
    {
        return $this->redis->lRange($this->log_name, 0, -1);
    }

    public function mute() {
        $this->redis->set($this->mute_name, true);
    }

    public function unmute() {
        $this->redis->del($this->mute_name);
    }
}
