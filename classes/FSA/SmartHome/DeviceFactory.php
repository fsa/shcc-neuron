<?php

namespace FSA\SmartHome;

class DeviceFactory
{
    private $plugins;

    public function __construct($plugins)
    {
        $this->plugins = $plugins;
    }

    public function create(string $plugin, string $hwid, string $class, ?array $properties)
    {
        if (!isset($this->plugins[$plugin])) {
            syslog(LOG_ERR, 'Не существующий плагин');
            return null;
        }
        $class_name = $this->plugins[$plugin] . 'Devices\\' . $class;
        if (!class_exists($class_name)) {
            syslog(LOG_ERR, 'Не существует класс устройства '.$class_name);
            return null;
        }
        $device = new $class_name;
        if (!($device instanceof DeviceInterface)) {
            syslog(LOG_ERR, 'Класс ' . $class_name . ' не обладает интерфейсом DeviceInterface');
            return null;
        }
        if ($properties) {
            $device->init($hwid, $properties);
        }
        return $device;
    }
}
