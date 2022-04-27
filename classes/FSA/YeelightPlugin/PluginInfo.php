<?php

namespace FSA\YeelightPlugin;

class PluginInfo 
{
    public function getName()
    {
        return "Yeelight";
    }

    public function getDescription()
    {
        return 'Обеспечивает поддержку светильников Yeelight при включении на этом оборудовании режима &laquo;Управление по локальной сети&raquo;.';
    }

    public function getDaemonInfo()
    {
        return [
            "class" => Daemon::class,
            "settings" => []
        ];
    }
}
