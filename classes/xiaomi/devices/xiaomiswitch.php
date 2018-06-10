<?php

/**
 * Датчики температуры, влажности и давления Xiaomi и Aqara
 */

namespace Xiaomi\Devices;

class XiaomiSwitch extends AbstractDevice {

    protected function updateParam($param,$value) {
        switch ($param) {
            case "click0":
                break;
            case "double_click":
                break;
            case "long_click_press":
                break;
            case "long_click_release":
                break;
            default:
                echo "$param => $value\n";
        }
    }

}
