<?php

return [
    'name'=>'TTS',
    'description'=>'Синтезатор речи.',
    'daemon'=>SmartHome\Module\Tts\Daemon::class,
    'daemon_settings'=>[
        'provider'=>SmartHome\Module\Tts\Settings::getProvider(),
        'pre_sound'=>'notification.mp3',
        'pre_sound_period'=>5,
        'play_sound_cmd'=>'mpg123 -q %s'
    ]
];
