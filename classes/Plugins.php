<?php

use Composer\Installer\PackageEvent;

class Plugins
{
    const CONFIG_PATH = '/../shcc-plugins.json';
    private static $plugins;

    public static function prePackageInstall(PackageEvent $event)
    {
        $plugin = self::searchPlugin($event);
        if (is_null($plugin)) {
            return;
        }
        self::addPlugin($plugin);
    }

    public static function prePackageUninstall(PackageEvent $event)
    {
        $plugin = self::searchPlugin($event);
        if (is_null($plugin)) {
            return;
        }
        self::removePlugin($plugin);
    }

    private static function searchPlugin($event)
    {
        $extra = $event->getOperation()->getPackage()->getExtra();
        if (!isset($extra['shcc-plugin'])) {
            return null;
        }
        $plugin = $extra['shcc-plugin'];
        if (!(isset($plugin['name']) and isset($plugin['namespace']))) {
            return null;
        }
        return $plugin;
    }

    private static function addPlugin($data)
    {
        self::get();
        self::$plugins[$data['name']] = $data['namespace'];
        self::set();
    }

    private static function removePlugin($data)
    {
        self::get();
        $key = array_search($data['namespace'], self::$plugins);
        if ($key !== false) {
            unset(self::$plugins[$key]);
        }
        self::set();
    }

    public static function get()
    {
        if (is_null(self::$plugins)) {
            $config_file = __DIR__ . self::CONFIG_PATH;
            self::$plugins = file_exists($config_file) ? json_decode(file_get_contents($config_file), true, 512, JSON_THROW_ON_ERROR) : [];
            if (!is_array(self::$plugins)) {
                throw new \Exception('Plugins config file format error');
            }
        }
        return self::$plugins;
    }

    public static function getPluginInfo($plugin_name) {
        $plugins = self::get();
        if (!key_exists($plugin_name, $plugins)) {
            return null;
        }
        $class_name = $plugins[$plugin_name].'PluginInfo';
        if (!class_exists($class_name)) {
            return null;
        }
        return new $class_name;
    }

    private static function set()
    {
        file_put_contents(__DIR__ . self::CONFIG_PATH, json_encode(self::$plugins));
    }
}
