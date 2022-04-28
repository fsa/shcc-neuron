<?php

namespace FSA\SmartHome\TTS;

class PluginInfo 
{
    public function getName()
    {
        return "TTS";
    }

    public function getDescription()
    {
        return 'Синтезатор речи.';
    }

    public function getDaemonInfo()
    {
        return [
            "class" => Daemon::class,
            "settings" => [
                'provider' => Settings::getProvider(),
                'pre_sound' => 'notification.mp3',
                'pre_sound_period' => 5,
                'play_sound_cmd' => 'mpg123 -q %s'
            ]
        ];
    }
}
