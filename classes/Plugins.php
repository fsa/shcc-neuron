<?php

class Plugins extends \FSA\SmartHome\Plugins
{
    protected static function constPluginsConfigFIle(): string
    {
        return App::getWorkDir() . 'shcc-plugins.json';
    }
}
