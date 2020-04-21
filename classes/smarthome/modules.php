<?php

namespace SmartHome;

use AppException;

class Modules {

    private $dirs;

    public function __construct() {
        $this->dirs=scandir(__DIR__.'/../');
    }

    public function fetch() {
        while ($entry=array_shift($this->dirs)) {
            if($entry=='.' or $entry=='..') {
                continue;
            }
            $filename=__DIR__.'/../'.$entry.'/moduleinfo.php';
            if(file_exists($filename)) {
                return (object) require $filename;
            }
        }
        return null;
    }

    public function closeCursor() {
        $this->dirs=null;
    }

}
