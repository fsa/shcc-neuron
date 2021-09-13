<?php

return [
    'name'=>'Yeelight',
    'description'=>'Обеспечивает поддержку светильников Yeelight при включении на этом оборудовании режима &laquo;Управление по локальной сети&raquo;.',
    'daemon'=>SmartHome\Module\Yeelight\Daemon::class
];