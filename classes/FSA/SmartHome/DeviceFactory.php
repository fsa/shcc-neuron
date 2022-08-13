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
            # Не существующий плагин
            echo "1";
            return null;
        }
        $class_name = $this->plugins[$plugin] . 'Devices\\' . $class;
        if (!class_exists($class_name)) {
            # Не существует класс устройства
            echo "2";
            return null;
        }
        $device = new $class_name;
        if (!($device instanceof DeviceInterface)) {
            # Класс не обладает интерфейсом DeviceInterface
            echo "3";
            return null;
        }
        if ($properties) {
            $device->init($hwid, $properties);
        }
        return $device;
    }
}
