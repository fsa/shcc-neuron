<?php

use FSA\YeelightPlugin\Daemon;

return [
    'name'=>'Yeelight',
    'description'=>'Обеспечивает поддержку светильников Yeelight при включении на этом оборудовании режима &laquo;Управление по локальной сети&raquo;.',
    'daemon'=>Daemon::class
];