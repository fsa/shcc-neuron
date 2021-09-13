<?php

/**
 * Telegram Bot API 5.0
 */

namespace Telegram\Entity;

class Contact extends AbstractEntity {

    public string $phone_number;
    public string $first_name;
    public ?string $last_name=null;
    public ?int $user_id=null;
    public ?string $vcard=null;

}
