<?php

class App extends FSA\SmartHome\App
{
    protected static function constVarPrefix(): string
    {
        return "shcc";
    }

    protected static function constSessionName(): string
    {
        return "shcc";
    }

    public static function constWorkDir(): string
    {
        return realpath(__DIR__ . '/../') . '/';
    }

    protected static function getContext(): ?array
    {
        return [
            'title' => 'SHCC',
            'dashboard' => static::getSettings('dashboard'),
            'session' => static::session()
        ];
    }

    public static function init()
    {
        parent::init();
        ini_set('syslog.filter', 'raw');
        openlog('shcc', LOG_PID | LOG_ODELAY, LOG_USER);
    }
}
