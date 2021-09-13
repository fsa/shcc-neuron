<?php

/**
 * Telegram Bot API 5.0
 */

namespace Telegram\Entity;

class MessageEntity extends AbstractEntity {

    public string $type;
    public int $offset;
    public int $length;
    public ?string $url=null;
    public ?User $user=null;
    public ?string $language=null;

}
