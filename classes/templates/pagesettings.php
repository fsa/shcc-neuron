<?php

/**
 * SHCC 0.7.0
 * 2020-12-25
 */

namespace Templates;

use httpResponse;

class PageSettings extends ContentPage {

    public function header() {
        parent::header();
        $pages=[
            'devices'=>'Устройства',
            'meters'=>'Датчики',
            'modules'=>'Модули',
            'users'=>'Пользователи'
        ];
        $uri=explode('/',getenv('REQUEST_URI'));
        httpResponse::showNavTabs('/settings/%s/', $pages, $uri[2]);
        echo "<br>";
    }

}