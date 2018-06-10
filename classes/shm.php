<?php

class Shm {

    const PROJ='a';
    const MEM_SIZE=65536;
    const CHMOD=0660;

    private $shm;

    public function __construct() {
        $file=ftok(__FILE__,self::PROJ);
        $this->shm=shm_attach($file,self::MEM_SIZE,self::CHMOD);
    }
    
    public function getVar(int $var_key) {
        return @shm_get_var($this->shm,$var_key);
    }

    public function setVar(int $var_key, &$variable) {
        shm_put_var($this->shm,$var_key,$variable);
    }

    public function removeVar(int $var_key) {
        shm_remove_var($this->shm,$var_key);
    }
}
