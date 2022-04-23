<?php

namespace SmartHome;

use App;

class Devices
{

    private $device;
    private $pdo;

    public function __construct()
    {
        $this->pdo = App::sql();
    }

    public static function get($uid)
    {
        $s = App::sql()->prepare('SELECT * FROM devices WHERE uid=?');
        $s->execute([$uid]);
        $db_dev = $s->fetchObject();
        if (!$db_dev) {
            return null;
        }
        $storage = new DeviceStorage;
        $device = $storage->get($db_dev->hwid);
        if (is_null($device)) {
            $entity = json_decode($db_dev->entity);
            $device = new $entity->classname;
            if (isset($entity->properties)) {
                $device->init($db_dev->hwid, $entity->properties);
            }
        }
        return $device;
    }

    public function create()
    {
        $this->device = new Entity\Device;
    }

    public function fetchDeviceByUid($uid)
    {
        $s = $this->pdo->prepare('SELECT * FROM devices WHERE uid=?');
        $s->execute([$uid]);
        $s->setFetchMode(\PDO::FETCH_CLASS, Entity\Device::class);
        $this->device = $s->fetch();
    }

    public function fetchDeviceByHwid($hwid)
    {
        $s = $this->pdo->prepare('SELECT * FROM devices WHERE hwid=?');
        $s->execute([$hwid]);
        $s->setFetchMode(\PDO::FETCH_CLASS, Entity\Device::class);
        $this->device = $s->fetch();
    }

    public function exists($except = false)
    {
        if ($except) {
            if ($this->device instanceof Entity\Device) {
                return;
            }
            throw new Exception('Отсутствует устройство');
        }
        return $this->device instanceof Entity\Device;
    }

    public function getDevice()
    {
        return $this->device;
    }

    public function setDevice(Entity\Device $device)
    {
        $this->device = $device;
    }

    public function setDeviceProperties(array $data)
    {
        $this->exists(true);
        foreach ($data as $param => $value) {
            $this->device->$param = $value;
        }
    }

    public function update($old_uid)
    {
        $this->exists(true);
        $values = get_object_vars($this->device);
        $keys = array_keys($values);
        foreach ($keys as &$key) {
            $key = $key . '=:' . $key;
        }
        $values['old_uid'] = $old_uid;
        $stmt = $this->pdo->prepare('UPDATE devices SET ' . join(',', $keys) . ' WHERE uid=:old_uid');
        return $stmt->execute($values);
    }

    public function insert()
    {
        $this->exists(true);
        $params = get_object_vars($this->device);
        return $this->pdo->insert('devices', $params, 'hwid');
    }

    public static function getDevicesStmt(): \PDOStatement
    {
        $s = App::sql()->query("SELECT uid, hwid, description, entity FROM devices d ORDER BY hwid");
        $s->setFetchMode(\PDO::FETCH_OBJ);
        return $s;
    }

    public static function getDevicesHwids(): array
    {
        $s = App::sql()->query('SELECT hwid FROM devices');
        return $s->fetchAll(\PDO::FETCH_COLUMN);
    }

    public static function getDeviceByHwid($id): Entity\Device
    {
        $s = App::sql()->prepare('SELECT * FROM devices WHERE hwid=?');
        $s->execute([$id]);
        $s->setFetchMode(\PDO::FETCH_CLASS, Entity\Device::class);
        return $s->fetch();
    }

    public static function getDeviceByUid($id): Entity\Device
    {
        $s = App::sql()->prepare('SELECT * FROM devices WHERE uid=?');
        $s->execute([$id]);
        $s->setFetchMode(\PDO::FETCH_CLASS, Entity\Device::class);
        return $s->fetch();
    }

    public static function getUidByHwid($hwid)
    {
        $s = App::sql()->prepare('SELECT uid FROM devices WHERE hwid=?');
        $s->execute([$hwid]);
        return $s->fetchColumn();
    }

    public static function getAllDevicesEntity()
    {
        $stmt = App::sql()->query('SELECT hwid, entity FROM devices ORDER BY hwid');
        $devices = [];
        while ($device = $stmt->fetchObject()) {
            $entity = json_decode($device->entity);
            $device_obj = new $entity->classname;
            $device_obj->init($device->hwid, $entity->properties ?? []);
            $devices[$device->hwid] = $device_obj;
        }
        return $devices;
    }

    public static function storeEvents($uid, $events, $ts = null)
    {
        foreach ($events as $property => $value) {
            Sensors::storeEvent($uid, $property, $value, $ts);
        }
    }

    public static function execEventsCustomScripts($uid, $events, $ts = null)
    {
        $custom_dir = __DIR__ . '/../../custom/events/';
        if (!file_exists($custom_dir . $uid . '.php')) {
            return;
        }
        chdir($custom_dir);
        $eventsListener = require $uid . '.php';
        $eventsListener->uid = $uid;
        foreach ($events as $event => $value) {
            $method = 'on_event_' . str_replace('@', '_', $event);
            if (method_exists($eventsListener, $method)) {
                $eventsListener->$method($value, $ts);
            }
        }
    }

    public static function processEvents($hwid, $events, $ts = null)
    {
        $uid = self::getDeviceByHwid($hwid);
        if (!$uid) {
            return;
        }
        try {
            self::storeEvents($uid, $events, $ts);
        } catch (\Exception $ex) {
            syslog(LOG_ERR, 'Ошибка при сохранении данных с датчиков:' . PHP_EOL . $ex);
        }
        try {
            self::execEventsCustomScripts($uid, $events, $ts);
        } catch (\Exception $ex) {
            syslog(LOG_ERR, 'Ошибка при выполнении пользовательского скрипта events/' . $uid . '.php:' . PHP_EOL . $ex);
        }
    }

}
