<?php
$tz='Asia/Yekaterinburg';
return [
    "site"=>[
        "title"=>"SHCC"
    ],
    "url"=>"http://127.0.0.1",
    "daemon-ip"=>"127.0.0.1",
    "fail2ban"=>true,
    "pdo"=>[
        "dsn"=>"pgsql:host=localhost;dbname=shcc",
        "username"=>"shcc",
        "password"=>"shcc",
        "init"=>[
            "SET TIMEZONE=\"$tz\""
        ]
    ],
    "session"=>[
        "name"=>"shcc-session",
        "uid"=>"shcc-uid",
        "token"=>"shcc-token",
        "time"=>2592000,
        "path"=>"/"
    ],
    "tts"=>[
        "pre_sound"=>"dingdong.mp3",
        "play_sound_cmd"=>"mpg123 -q %s"
    ],
    "home"=>[
        "city"=>"Екатеринбург",
        "city_en"=>"Yekaterinburg",
        "lat"=>56.83827,
        "lon"=>60.60345
    ],
    "timezone"=>$tz,
    "admins"=>['admin'],
    "debug"=>true
];