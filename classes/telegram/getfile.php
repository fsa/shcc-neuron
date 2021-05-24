<?php

/**
 * Telegram Bot API 5.0
 */

namespace Telegram;

class GetFile extends Query {

    public $file_id;

    private $file;

    public function __construct(string $file_id=null) {
        if (!is_null($file_id)) {
            $this->file_id=$file_id;
        }
    }

    public function buildQuery(): array {
        if (is_null($this->file_id)) {
            throw new Exception('Required: file_id');
        }
        return parent::buildQuery();
    }

    public function getFileContent() {
        if (is_null($this->file_id)) {
            throw new Exception('Required: file_id');
        }
        $api_url='https://api.telegram.org/file/bot'.self::$token;
        $file=$this->httpPost();
        if(!$file->ok) {
            return null;
        }
        $this->file=new Entity\File((array)$file->result);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $api_url.'/'.$file->result->file_path);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        if(isset(self::$proxy)) {
            curl_setopt($ch, CURLOPT_PROXY, self::$proxy);
        }
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }

    public function getInfo(): Entity\File {
        return $this->file;
    }

}
