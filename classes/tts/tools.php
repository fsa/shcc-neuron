<?php

namespace Tts;

class Tools {

    public static function ruDegC($temperature) {
        $tempw=abs(round($temperature));
        $units='градусов';
        if ($tempw<11 or $tempw>14) {
            $tempw=$tempw%10;
            if ($tempw==1) {
                $units='градус';
            }
            if ($tempw>=2 and $tempw<=4) {
                $units='градуса';
            }
        }
        return sprintf("%+.0f $units цельсия",$temperature);
    }

}
