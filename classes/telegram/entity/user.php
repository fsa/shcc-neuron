<?php

/**
 * Telegram Bot API 5.0
 */

namespace Telegram\Entity;

class User extends AbstractEntity {

    public int $id;
    public bool $is_bot;
    public string $first_name;
    public ?string $last_name=null;
    public ?string $username=null;
    public ?string $language_code=null;
    public ?bool $can_join_groups=null;
    public ?bool $can_read_all_group_messages=null;
    public ?bool $supports_inline_queries=null;

}
