<?php

/**
 * Telegram Bot API 5.0
 */

namespace Telegram;

class GetUpdates extends Query {

    private int $offset;
    private int $limit;
    private int $timeout;
    private array $allowed_updates;

    public function setOffset(int $offset) {
        $this->offset=$offset;
    }

    public function setLimit(int $limit) {
        $this->limit=$limit;
    }

    public function setTimeout(int $timeout) {
        $this->timeout=$timeout;
    }

    public function setAllowedUpdates(array $allowed_updates) {
        $this->allowed_updates=$allowed_updates;
    }

    public function addAllowedUpdates(string $allowed_update) {
        $this->allowed_updates[]=$allowed_update;
    }

}
