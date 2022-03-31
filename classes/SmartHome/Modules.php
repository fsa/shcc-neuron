<?php

namespace SmartHome;

use FSA\Neuron\Settings;

class Modules {

    private $modules;
    private $fetch_modules;

    public function __construct() {
        $this->modules=[];
        $this->dirs=scandir(__DIR__.'/module/');
        while ($entry=array_shift($this->dirs)) {
            if($entry=='.' or $entry=='..') {
                continue;
            }
            $filename=__DIR__.'/module/'.$entry.'/moduleinfo.php';
            if(file_exists($filename)) {
                $module=(object) require $filename;
                $this->modules[strtolower($module->name)]=$module;
            }
        }
    }

    public function isModuleExists($name) {
        return isset($this->modules[$name]);
    }

    public function isDaemonActive($name) {
        $daemons=$this->getActiveDaemonVar();
        return array_search($name, $daemons)!==false;
    }

    public function getDaemonClass($name) {
        if(isset($this->modules[$name]->daemon)) {
            return $this->modules[$name]->daemon;
        }
        return null;
    }

    public function getDaemonSettings($name) {
        $params=Settings::get(strtolower($name), []);
        if(isset($this->modules[$name]->daemon_settings)) {
            $params=array_merge($this->modules[$name]->daemon_settings, $params);
        }
        return $params;

    }

    public function enableDaemon($name) {
        $daemons=$this->getActiveDaemonVar();
        if(array_search($name, $daemons)!==false) {
            throw new UserException('Демон уже активирован!');
        }
        if(!isset($this->modules[$name])) {
            throw new UserException('Модуль не найден!');
        }
        if(!isset($this->modules[$name]->daemon)) {
            throw new UserException('У модуля отсутствует демон!');
        }
        if(!class_exists($this->modules[$name]->daemon)) {
            throw new UserException('Класс, обеспечивающший работу демона, не найден!');
        }
        $daemons[]=$name;
        $this->setActiveDaemonVar($daemons);
    }

    public function disableDaemon($name) {
        $daemons=$this->getActiveDaemonVar();
        $key=array_search($name, $daemons);
        if($key===false) {
            throw new UserException('Демон уже отключен.');
        }
        unset($daemons[$key]);
        $this->setActiveDaemonVar($daemons);
    }

    private function getActiveDaemonVar() {
        $daemons=Vars::getJson('System.Daemons', true);
        if(is_array($daemons)) {
            return $daemons;
        }
        return [];
    }

    private function setActiveDaemonVar(array $value) {
        Vars::setJson('System.Daemons', $value);
    }

    public function query() {
        $this->fetch_modules=$this->modules;
    }

    public function fetch() {
        return array_shift($this->fetch_modules);
    }

    public function fetchObject() {
        $module=array_shift($this->fetch_modules);
        return $module?(object)$module:null;
    }

    public function rowCount() {
        return count($this->modules);
    }

    public function closeCursor() {
        $this->fetch_modules=null;
    }

}
