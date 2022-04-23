<?php

namespace SmartHome;

use Composer\Installer\PackageEvent;

class Plugins
{
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
        $plugin['config'] = $event->getComposer()->getConfig()->get('vendor-dir') . '/../shcc-plugins.json';
        return $plugin;
    }

    private static function addPlugin($data)
    {
        $config = file_exists($data['config']) ? json_decode(file_get_contents($data['config']), true, 512, JSON_THROW_ON_ERROR) : [];
        if (!is_array($config)) {
            $config = [];
        }
        $config[$data['name']] = $data['namespace'];
        file_put_contents($data['config'], json_encode($config));
    }

    private static function removePlugin($data)
    {
        if (!file_exists($data['config'])) {
            echo 'Файл со списком плагинов не существует' . PHP_EOL;
            return;
        }
        $config = json_decode(file_get_contents($data['config']), true, 512, JSON_THROW_ON_ERROR);
        $key = array_search($data['namespace'], $config);
        if ($key !== false) {
            unset($config[$key]);
        }
        file_put_contents($data['config'], json_encode($config));
    }
}
