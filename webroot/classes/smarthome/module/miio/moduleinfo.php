<?php

return [
    'name'=>'miIO',
    'description'=>"Обеспечивает управление устройствами Xiaomi MiHome через протокол miIO.",
    'daemon'=>SmartHome\Module\miIO\Daemon::class
];