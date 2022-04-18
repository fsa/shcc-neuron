<?php

namespace Templates;

use App;

class PageSettings extends Main
{

    public function showHeader()
    {
        parent::showHeader();
        $pages = [
            'devices' => 'Устройства',
            'sensors' => 'Датчики',
            'modules' => 'Модули',
            'users' => 'Пользователи'
        ];
        $uri = explode('/', getenv('REQUEST_URI'));
        App::response()->showNavTabs('/settings/%s/', $pages, $uri[2]);
        echo "<br>";
    }
}
