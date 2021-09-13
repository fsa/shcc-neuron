<?php

/**
 * Telegram Bot API 5.0
 */

namespace Telegram\Entity;

class ForceReply extends AbstractEntity implements ReplyMarkupInterface {

    public bool $force_reply;
    public ?bool $selective=null;

    public function __construct(bool $force_reply=true) {
        $this->force_reply=$force_reply;
    }

    public function setSelective(bool $selective=true) {
        $this->selective=$selective;
    }

}
