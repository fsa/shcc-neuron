<?php

class MemoryStorage {

    const PROJ='a';
    const MEM_SIZE=65536;
    const CHMOD=0600;

    private $shm;

    public function __construct() {
        $file=ftok(__FILE__,self::PROJ);
        $this->shm=shm_attach($file,self::MEM_SIZE,self::CHMOD);
        
    }
    
    public function getArray(string $name) {
        $array=$this->getVar($name);
        if ($array===false or !is_array($array)) {
            $array=[];
        }
        return $array;
    }
    
    public function getVar(string $name) {
        return @shm_get_var($this->shm,$this->getKeyByVarName($name));
    }

    public function setVar(string $name, &$variable) {
        shm_put_var($this->shm,$this->getKeyByVarName($name),$variable);
    }

    public function removeVar(string $name) {
        shm_remove_var($this->shm,$this->getKeyByVarName($name));
    }
    
    private function getKeyByVarName($name) {
        $keys=@shm_get_var($this->shm,1);
        if(!is_array($keys)) {
            $keys=[];
        }
        if(!isset($keys[$name])) {
            $keys[$name]=sizeof($keys)+2;
            shm_put_var($this->shm,1,$keys);
        }
        return $keys[$name];
    }
}
