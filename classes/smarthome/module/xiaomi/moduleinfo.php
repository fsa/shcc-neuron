<?php

return [
    'name'=>'Xiaomi',
    'description'=>'Обеспечивает поддержу оборудования Xiaomi через шлюз при включении на шлюзе &laquo;Протокола связи локальной сети&raquo;.',
    'daemon'=>SmartHome\Module\Xiaomi\Daemon::class,
    'daemon_settings'=>[
        'ip'=>Xiaomi\Devices\XiaomiGateway::MULTICAST_ADDRESS,
        'port'=>Xiaomi\Devices\XiaomiGateway::MULTICAST_PORT
    ]
];