<?php

/**
 * Telegram Bot API 5.0
 */

declare(strict_types=1);

namespace Telegram\Entity;

class Update extends AbstractEntity {

    public int $update_id;
    public ?Message $message=null;
    public ?Message $edited_message=null;
    public ?Message $channel_post=null;
    public ?Message $edited_channel_post=null;
    public ?InlineQuery $inline_query=null;
    public ?ChosenInlineResult $chosen_inline_result=null;
    public ?CallbackQuery $callback_query=null;
    public ?ShippingQuery $shipping_query=null;
    public ?PreCheckoutQuery $pre_checkout_query=null;
    public ?Poll $poll=null;
    public ?PollAnswer $poll_answer=null;

}
