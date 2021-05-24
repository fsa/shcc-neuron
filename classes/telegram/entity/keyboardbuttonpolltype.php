<?php

/**
 * Telegram Bot API 4.9
 */

namespace Telegram\Entity;

class KeyboardButtonPollType extends AbstractEntity {

    public string $type;
    
    public function __construct(string $type) {
        $this->type=$type;
    }
    
    public function setType(string $type) {
        $this->type=$type;
    }

}
