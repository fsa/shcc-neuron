<?php

/**
 * Telegram Bot API 5.0
 */

namespace Telegram\Entity;

class ResponseParameters extends AbstractEntity {

    public ?int $migrate_to_chat_id=null;
    public ?int $retry_after=null;

}
