<?php

namespace FSA\XiaomiPlugin;

class PluginInfo 
{
    public function getName()
    {
        return "Xiaomi";
    }

    public function getDescription()
    {
        return 'Обеспечивает поддержу оборудования Xiaomi через шлюз при включении на шлюзе &laquo;Протокола связи локальной сети&raquo;.';
    }

    public function getDaemonInfo()
    {
        return [
            "class" => Daemon::class,
            "settings" => [
                'ip' => Socket::MULTICAST_ADDRESS,
                'port' => Socket::MULTICAST_PORT
            ]
        ];
    }
}
