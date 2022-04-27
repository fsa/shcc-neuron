<?php

namespace FSA\SmartHome;

use PDO;

class DeviceDatabase
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function get($uid)
    {
        $s = $this->pdo->prepare('SELECT * FROM devices WHERE uid=?');
        $s->execute([$uid]);
        return $s->fetchObject(Entity\Device::class);
    }
/*
    public function search($plugin, $hwid)
    {
        $s = $this->pdo->prepare('SELECT * FROM devices WHERE plugin=? AND hwid=?');
        $s->execute([$plugin, $hwid]);
        return $s->fetchObject(Entity\Device::class);
        $this->device = $s->fetch();
    }
*/
/*
    public function searchUid($plugin, $hwid)
    {
        $s = $this->pdo->prepare('SELECT uid FROM devices WHERE plugin=? AND hwid=?');
        $s->execute([$plugin, $hwid]);
        return $s->fetchColumn();
    }
*/
    public function getAll($plugin = null)
    {
        if (is_null($plugin)) {
            $s = $this->pdo->query('SELECT * FROM devices ORDER BY hwid');
        } else {
            $s = $this->pdo->prepare('SELECT * FROM devices WHERE plugin=? ORDER BY hwid');
            $s->execute([$plugin]);
        }
        $s->setFetchMode(PDO::FETCH_CLASS, Entity\Device::class);
        return $s;
    }

}
