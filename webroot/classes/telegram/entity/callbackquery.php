<?php

/**
 * Telegram Bot API 5.0
 */

namespace Telegram\Entity;

class CallbackQuery extends AbstractEntity {

    public string $id;
    public User $from;
    public ?Message $message=null;
    public ?string $inline_message_id=null;
    public ?string $chat_instance=null;
    public ?string $data=null;
    public ?string $game_short_name=null;

}
