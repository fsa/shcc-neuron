<?php

namespace SmartHome;

use Composer\Autoload\ClassLoader;
use Composer\Installer\PackageEvent;

class Plugins
{
    const CONFIG_PATH = '/../shcc-plugins.json';
    private static $plugins;
    private static $vendor_dir;

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
        self::$vendor_dir = $event->getComposer()->getConfig()->get('vendor-dir');
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
            if (is_null(self::$vendor_dir)) {
                $reflection = new \ReflectionClass(ClassLoader::class);
                self::$vendor_dir = dirname($reflection->getFileName(), 2);
            }
            $config_file = self::$vendor_dir . self::CONFIG_PATH;
            self::$plugins = file_exists($config_file) ? json_decode(file_get_contents($config_file), true, 512, JSON_THROW_ON_ERROR) : [];
            if (!is_array(self::$plugins)) {
                throw new \Exception('Plugins config file format error');
            }
        }
        return self::$plugins;
    }

    private static function set()
    {
        if (is_null(self::$vendor_dir)) {
            throw new \Exception('Get config first');
        }
        $config_file = self::$vendor_dir . self::CONFIG_PATH;
        file_put_contents($config_file, json_encode(self::$plugins));
    }
}
