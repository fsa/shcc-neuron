<?php

namespace FSA\SmartHome\Capability;

interface ColorTInterface
{
    function setCT(int $ct_value, int $line = 0);
    function getCT(int $line = 0): int;
}
