<?php

/**
 * Telegram Bot API 5.0
 */

namespace Telegram\Entity;

class Chat extends AbstractEntity {

    public int $id;
    public string $type;
    public ?string $title=null;
    public ?string $username=null;
    public ?string $first_name=null;
    public ?string $last_name=null;
    public ?ChatPhoto $photo=null;
    public ?string $bio=null;
    public ?string $description=null;
    public ?string $invite_link=null;
    public ?Message $pinned_message=null;
    public ?ChatPermission $permissions=null;
    public ?int $slow_mode_delay=null;
    public ?string $sticker_set_name=null;
    public ?bool $can_set_sticker_set=null;
    public ?int $linked_chat_id=null;
    public ?ChatLocation $location=null;

}
