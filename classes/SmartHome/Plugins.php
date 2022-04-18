<?php

namespace SmartHome;

use Composer\Installer\PackageEvent;

class Plugins
{
    public static function postPackageInstall(PackageEvent $event)
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
        $package = $event->getOperation()->getPackage();
        if (array_search('shcc', $package->getKeywords()) === false) {
            return null;
        }
        $autoload = $package->getAutoload();
        if (!isset($autoload['psr-4'])) {
            return null;
        }
        return ['namespace' => array_key_first($autoload['psr-4']), 'config' => $event->getComposer()->getConfig()->get('vendor-dir') . '/../shcc-plugins.json'];
    }

    private static function addPlugin($data)
    {
        $config = file_exists($data['config']) ? json_decode(file_get_contents($data['config']), true, 512, JSON_THROW_ON_ERROR) : [];
        if (!is_array($config)) {
            $config = [];
        }
        if (array_search($data['namespace'], $config) === false) {
            $config[] = $data['namespace'];
        }
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
