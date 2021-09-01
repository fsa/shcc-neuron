<?php

class LangTools {

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
        return sprintf("%+.0f $units Цельсия",$temperature);
    }

        public function getWindDirectionAbbr($deg) {
        if ($deg<22) {
            return 'C';
        } elseif ($deg<68) {
            return 'СЗ';
        } elseif ($deg<112) {
            return 'З';
        } elseif ($deg<158) {
            return 'ЮЗ';
        } elseif ($deg<202) {
            return 'Ю';
        } elseif ($deg<248) {
            return 'ЮВ';
        } elseif ($deg<292) {
            return 'В';
        } elseif ($deg<338) {
            return 'СВ';
        } else {
            return 'С';
        }
    }

    private function getWindDirectionName($deg) {
        if ($deg<22) {
            return 'северный';
        } elseif ($deg<68) {
            return 'северо-западный';
        } elseif ($deg<112) {
            return 'западный';
        } elseif ($deg<158) {
            return 'юго-западный';
        } elseif ($deg<202) {
            return 'южный';
        } elseif ($deg<248) {
            return 'юго-восточный';
        } elseif ($deg<292) {
            return 'восточный';
        } elseif ($deg<338) {
            return 'северо-восточный';
        } else {
            return 'северный';
        }
    }

}
