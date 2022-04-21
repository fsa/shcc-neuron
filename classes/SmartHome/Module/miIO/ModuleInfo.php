<?php

use FSA\miIOPlugin\Daemon;

return [
    'name'=>'miIO',
    'description'=>"Обеспечивает управление устройствами Xiaomi MiHome через протокол miIO.",
    'daemon'=>Daemon::class
];