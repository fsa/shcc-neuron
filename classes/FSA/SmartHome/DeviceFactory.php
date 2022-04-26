<?php

namespace FSA\SmartHome;

class DeviceFactory
{
    private $plugins;

    public function __construct($plugins)
    {
        $this->plugins = $plugins;
    }

    public function create(string $plugin, string $hwid, string $class, array $properties)
    {
        if (!isset($this->plugins[$plugin])) {
            # Не существующий плагин
            return null;
        }
        $class_name = $this->plugins[$plugin] . 'Devices\\' . $class;
        if (!class_exists($class_name)) {
            # Не существует класс устройства
            return null;
        }
        $device = new $class_name;
        if (!($device instanceof DeviceInterface)) {
            # Класс не обладает интерфейсом DeviceInterface
            return null;
        }
        $device->init($hwid, $properties);
        return $device;
    }
}
