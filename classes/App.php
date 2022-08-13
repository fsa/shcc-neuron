<?php

use FSA\Neuron\{PostgreSQL, RedisDB, ResponseHtml, ResponseJson, Session, SessionStorageRedis};

class App
{
    const REDIS_PREFIX = 'shcc';

    private static $db;
    private static $redis;
    private static $response;
    private static $settings;
    private static $session;

    public static function init($log_tag = 'shcc')
    {
        ini_set('syslog.filter', 'raw');
        openlog($log_tag, LOG_PID | LOG_ODELAY, LOG_USER);
        if ($tz = getenv('TZ')) {
            date_default_timezone_set($tz);
        }
    }

    public static function initHtml($main_template = Templates\Main::class): ResponseHtml
    {
        self::init();
        self::$response = new ResponseHtml($main_template, Templates\Login::class, Templates\Message::class);
        self::$response->setContext(['title' => 'SHCC', 'dashboard' => self::getSettings('dashboard'), 'session' => self::session()]);
        set_exception_handler([__CLASS__, 'exceptionHandler']);
        return self::$response;
    }

    public static function initJson(): ResponseJson
    {
        self::init();
        self::$response = new ResponseJson;
        set_exception_handler([__CLASS__, 'exceptionHandler']);
        return self::$response;
    }

    public static function response()
    {
        return self::$response;
    }

    public static function getSettings(string $name, $default_value = null)
    {
        if (is_null(self::$settings)) {
            self::$settings = require __DIR__ . '/../settings.php';
        }
        return self::$settings[$name] ?? $default_value;
    }

    public static function sql(): PostgreSQL
    {
        if (is_null(self::$db)) {
            self::$db = new PostgreSQL(getenv('DATABASE_URL'));
            if ($tz = getenv('TZ')) {
                self::$db->query("SET TIMEZONE=\"$tz\"");
            }
        }
        return self::$db;
    }

    public static function redis(): Redis
    {
        if (is_null(self::$redis)) {
            self::$redis = new RedisDB(getenv('REDIS_URL'));
        }
        return self::$redis;
    }

    public static function session(): Session
    {
        if (is_null(self::$session)) {
            $storage = new SessionStorageRedis(self::REDIS_PREFIX, self::redis());
            self::$session = new Session(getenv('SESSION_NAME') ?: 'shcc', $storage);
            self::$session->setCookieOptions([
                'path' => getenv('SESSION_PATH'),
                'domain' => getenv('SESSION_DOMAIN'),
                'secure' => !empty(getenv('SESSION_SECURE')),
                'httponly' => !empty(getenv('SESSION_HTTPONLY')),
                'samesite' => getenv('SESSION_SAMESITE')
            ]);
            if ($admins = getenv('APP_ADMINS')) {
                self::$session->setAdmins(explode(',', $admins));
            }
        }
        return self::$session;
    }

    public static function login($login, $password)
    {
        $user = new User(self::sql());
        if (!$user->login($login, $password)) {
            self::response()->returnError(200, 'Неверное имя пользователя или пароль.');
            exit;
        }
        self::session()->login($user);
    }

    public static function logout()
    {
        self::session()->logout();
    }

    public static function getVar($name)
    {
        return self::redis()->get(self::REDIS_PREFIX . ':vars:' . $name);
    }

    public static function setVar($name, $value)
    {
        App::redis()->set(self::REDIS_PREFIX . ':vars:' . $name, $value);
    }

    public static function getVarJson($name, $array = true)
    {
        $val = self::getVar($name);
        return json_decode($val, $array);
    }

    public static function setVarJson($name, $object)
    {
        self::setVar($name, json_encode($object));
    }

    public static function dropVar($name)
    {
        return App::redis()->del(self::REDIS_PREFIX . ':vars:' . $name);
    }

    public static function exceptionHandler($ex)
    {
        $class = get_class($ex);
        $class_parts = explode('\\', $class);
        if (end($class_parts) == 'UserException') {
            self::response()->returnError(200, $ex->getMessage());
        } else if (end($class_parts) == 'HtmlException') {
            self::response()->returnError($ex->getCode(), $ex->getMessage());
        } else if (getenv('DEBUG')) {
            error_log($ex, 0);
            self::response()->returnError(500, '<pre>' . (string) $ex . '</pre>');
        } else {
            error_log($ex, 0);
            self::response()->returnError(500, 'Внутренняя ошибка сервера');
        }
        exit;
    }
}
