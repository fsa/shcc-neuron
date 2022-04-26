<?php

namespace SmartHome\Capability;

interface ColorTInterface {

    function setCT(int $ct_value);
    
    function getCT(): int;
}
