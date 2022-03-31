<?php

namespace Templates;

use FSA\Neuron\HttpResponse;

class PageSettings extends Main {

    public function header() {
        parent::header();
        $pages=[
            'devices'=>'Устройства',
            'sensors'=>'Датчики',
            'modules'=>'Модули',
            'users'=>'Пользователи'
        ];
        $uri=explode('/',getenv('REQUEST_URI'));
        HttpResponse::showNavTabs('/settings/%s/', $pages, $uri[2]);
        echo "<br>";
    }

}