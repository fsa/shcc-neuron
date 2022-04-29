<?php

namespace FSA\SmartHome;

use PDO;
use SmartHome;

class SensorDatabase
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function storeEvent($device_uid, $property, $value, $ts = null): bool
    {
        $s = $this->pdo->prepare('SELECT id, uid, history FROM sensors WHERE device_property=?');
        $s->execute([$device_uid . '@' . $property]);
        $sensor = $s->fetchObject();
        if (!$sensor) {
            return false;
        }
        SmartHome::sensorStorage()->set($sensor->uid, $value, $ts);
        if (is_null($sensor->history)) {
            return false;
        }
        if (is_null($ts)) {
            $ts = time();
        }
        if (is_bool($value)) {
            $value = intval($value);
        }
        #TODO: проверить отсутствие записи с указанным ts
        $s = $this->pdo->prepare('INSERT INTO ' . $sensor->history . ' (sensor_id, value, timestamp) VALUES (?, ?, ?)');
        $datetime = date('c', $ts);
        $s->execute([$sensor->id, $value, $datetime]);
        return true;
    }

    public function getHistory(string $uid, $from = null, $to = null)
    {
        $s = $this->pdo->prepare('SELECT * FROM sensors WHERE uid=?');
        $s->execute([$uid]);
        $sensor = $s->fetchObject();
        if (!$sensor or is_null($sensor->history)) {
            return [];
        }
        $params = ["sensor_id" => $sensor->id];
        if ($from) {
            $params['from'] = date('c', $from);
            if ($to) {
                $period = ' AND timestamp BETWEEN :from AND :to';
                $params['to'] = date('c', $to);
            } else {
                $period = ' AND timestamp>=:from';
            }
        } else {
            $period = '';
        }
        $stmt = $this->pdo->prepare('SELECT ROUND(EXTRACT(EPOCH FROM timestamp)*1000) AS ts,value FROM ' . $sensor->history . ' WHERE sensor_id=:sensor_id' . $period . ' ORDER BY timestamp');
        $stmt->execute($params);
        return ['name' => $sensor->description, 'unit' => Sensor::getPropertyUnit($sensor->property), 'data' => $stmt->fetchAll(PDO::FETCH_NUM)];
    }

    public function getHistoryJson(string $uid, $from = null, $to = null)
    {
        $s = $this->pdo->prepare('SELECT * FROM sensors WHERE uid=?');
        $s->execute([$uid]);
        $sensor = $s->fetchObject();
        if (!$sensor or is_null($sensor->history)) {
            return [];
        }
        $params = ["sensor_id" => $sensor->id];
        if ($from) {
            $params['from'] = date('c', $from);
            if ($to) {
                $period = ' AND timestamp BETWEEN :from AND :to';
                $params['to'] = date('c', $to);
            } else {
                $period = ' AND timestamp>=:from';
            }
        } else {
            $period = '';
        }
        $stmt = $this->pdo->prepare('SELECT json_build_object(\'name\', \'' . $sensor->description . '\',\'unit\', \'' . Sensor::getPropertyUnit($sensor->property) . '\',\'data\', (SELECT array_agg(array[ts, value]) FROM (SELECT ROUND(EXTRACT(EPOCH FROM timestamp)*1000) AS ts, value FROM ' . $sensor->history . ' WHERE sensor_id=:sensor_id' . $period . ' ORDER BY timestamp) AS data))');
        $stmt->execute($params);
        return $stmt->fetchColumn();
    }

    public function getAll()
    {
        $s = $this->pdo->query('SELECT * FROM sensors ORDER BY uid');
        $s->setFetchMode(PDO::FETCH_CLASS, Entity\Sensor::class);
        return $s;
    }
}
