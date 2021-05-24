<?php

/**
 * Telegram Bot API 5.0
 */

namespace Telegram;

abstract class Query {

    protected const API_URL='https://api.telegram.org/bot';

    protected static $token;
    protected static $proxy;

    public static function init(string $token, string $proxy=null) {
        self::$token=$token;
        self::$proxy=$proxy;
    }

    public function getActionName(): string {
        $class=explode('\\', get_class($this));
        return lcfirst(end($class));
    }

    public function buildQuery(): array {
        return array_filter(get_object_vars($this), fn($element)=>!empty($element));
    }

    public function httpPost(): object {
        $ch=curl_init();
        curl_setopt($ch, CURLOPT_URL, self::API_URL.self::$token.'/'.$this->getActionName());
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        if (isset(self::$proxy)) {
            curl_setopt($ch, CURLOPT_PROXY, self::$proxy);
        }
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $this->buildQuery());
        $result=curl_exec($ch);
        curl_close($ch);
        if ($result===false) {
            throw new Exception('Не удалось поучить данные для '.$this->getActionName());
        }
        return json_decode($result);
    }

    public function httpPostJson(): object {
        $query=json_encode($this->buildQuery(), JSON_UNESCAPED_UNICODE|JSON_NUMERIC_CHECK);
        $ch=curl_init();
        curl_setopt($ch, CURLOPT_URL, self::API_URL.self::$token.'/'.$this->getActionName());
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        if (isset(self::$proxy)) {
            curl_setopt($ch, CURLOPT_PROXY, self::$proxy);
        }
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
           'Content-Type: application/json',
           'Content-Length: ' . strlen($query))
        );
        curl_setopt($ch, CURLOPT_CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $query);
        $result=curl_exec($ch);
        curl_close($ch);
        if ($result===false) {
            throw new Exception('Не удалось поучить данные для '.$this->getActionName());
        }
        return json_decode($result);
    }

    public function httpGet(): object {
        $ch=curl_init();
        curl_setopt($ch, CURLOPT_URL, self::API_URL.self::$token.'/'.$this->getActionName().'?'.http_build_query($this->buildQuery()));
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        if (isset(self::$proxy)) {
            curl_setopt($ch, CURLOPT_PROXY, self::$proxy);
        }
        $result=curl_exec($ch);
        curl_close($ch);
        if ($result===false) {
            throw new Exception('Не удалось поучить данные для '.$this->getActionName());
        }
        return json_decode($result);
    }

    public function webhookReplyJson(): void {
        $query=$this->buildQuery();
        $query['method']=$this->getActionName();
        header('Content-Type: application/json');
        echo json_encode($query, JSON_UNESCAPED_UNICODE);
    }

}
