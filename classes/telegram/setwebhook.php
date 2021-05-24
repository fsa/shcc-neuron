<?php

/**
 * Telegram Bot API 5.0
 */

namespace Telegram;

class SetWebhook extends Query {

    public string $url;
    public $certificate;
    public string $ip_address;
    public int $max_connections;
    public array $allowed_updates;
    public bool $drop_pending_updates;

    public function __construct(string $url) {
        $this->setUrl($url);
    }

    public function setUrl(string $url) {
        $this->url=$url;
    }

    public function setCertificate($certificate) {
        $this->certificate=$certificate;
    }

    public function setIpAddress(string $ip_address) {
        $this->ip_address=$ip_address;
    }

    public function setMaxConnections(int $max_connections) {
        if ($max_connections<1 or $max_connections>100) {
            throw new \Exception('Max_connections - 1-100');
        }
        $this->max_connections=$max_connections;
    }

    public function setAllowedUpdates(array $allowed_updates) {
        $this->allowed_updates=$allowed_updates;
    }

    public function addAllowedUpdates(string $allowed_update) {
        if(!isset($this->allowed_updates)) {
            $this->allowed_updates=[];
        }
        $this->allowed_updates[]=$allowed_update;
    }

    public function setDropPendingUpdates(bool $drop_pending_updates) {
        $this->drop_pending_updates=$drop_pending_updates;
    }

}
