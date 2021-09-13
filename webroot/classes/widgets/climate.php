<?php

namespace Widgets;

use httpResponse;

class Climate {

    private $sensors=[];

    public function __construct() {

    }

    public function addPressureSensor($title, $device_name) {
        $this->sensors[]=['pressure', $title, $device_name];
    }


    public function addHTSensor($title, $device_name) {
        $this->sensors[]=['ht', $title, $device_name];
    }

    public function show() {
        $body=[];
        foreach ($this->sensors as $sensor) {
            $title=$sensor[1];
            $device_name=$sensor[2];
            switch($sensor[0]) {
                case 'ht':
                    $body[]=<<< EOS
<span device_name="{$device_name}" device_property="last_update"></span> $title:
EOS;
                    $body[]=<<< EOS
<span style="font-size: 2rem;" class="device-state-off" device_name="{$device_name}"><span device_name="{$device_name}" device_property="temperature">-</span>&deg;C, <span device_name="{$device_name}" device_property="humidity">-</span>%</span>
EOS;
                    break;
                case 'pressure':
                    $body[]=<<< EOS
<span style="font-size: 2rem;"><span device_name="{$device_name}" device_property="pressure">-</span> мм.рт.ст.</span>
EOS;
                    break;
            }
        }
        httpResponse::showCard('Климат', join("<br>\n", $body));

    }
}