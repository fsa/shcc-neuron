<?php

use FSA\Xiaomi\Socket;
use FSA\XiaomiPlugin\Daemon;

return [
    'name'=>'Xiaomi',
    'description'=>'Обеспечивает поддержу оборудования Xiaomi через шлюз при включении на шлюзе &laquo;Протокола связи локальной сети&raquo;.',
    'daemon'=>Daemon::class,
    'daemon_settings'=>[
        'ip'=>Socket::MULTICAST_ADDRESS,
        'port'=>Socket::MULTICAST_PORT
    ]
];