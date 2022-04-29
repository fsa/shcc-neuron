<?php

namespace FSA\SmartHome;

class SensorStorage
{
    private $redis;
    private $key_prefix;

    public function __construct($redis, $key_prefix)
    {
        $this->redis = $redis;
        $this->key_prefix = $key_prefix;
    }

    public function set(string $uid, $value, $ts = null)
    {
        $this->redis->set($this->key_prefix . ':' . $uid, json_encode(["value" => $value, "ts" => is_null($ts) ? time() : $ts]));
    }

    public function get(string $uid)
    {
        $sensor = $this->redis->get($this->key_prefix . ':' . $uid);
        return $sensor ? json_decode($sensor) : null;
    }

    public function rename(string $old_uid, string $uid)
    {
        if (!$this->redis->exists($this->key_prefix . ':' . $old_uid)) {
            return;
        }
        $this->redis->rename($this->key_prefix . ':' . $old_uid, $this->key_prefix . ':' . $uid);
    }
}
