<?php

/**
 * Telegram Bot API 5.0
 */

namespace Telegram\Entity;

class LoginUrl extends AbstractEntity {

    public string $url;
    public ?string $forward_text=null;
    public ?string $bot_username=null;
    public ?bool $request_write_access=null;

}
