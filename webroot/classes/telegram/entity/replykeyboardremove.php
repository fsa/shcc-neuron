<?php

/**
 * Telegram Bot API 5.0
 */

namespace Telegram\Entity;

class ReplyKeyboardRemove extends AbstractEntity implements ReplyMarkupInterface {

    public bool $remove_keyboard=true;
    public ?bool $selective=null;

    public function __construct(bool $selective=null) {
        if (!is_null($selective)) {
            $this->selective=$selective;
        }
    }

    public function __toString() {
        return json_encode($this->jsonSerialize(), JSON_UNESCAPED_UNICODE);
    }

}
