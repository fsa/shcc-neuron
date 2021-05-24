<?php

/**
 * Telegram Bot API 5.0
 */

namespace Telegram\Entity;

class PollOption extends AbstractEntity {

    public string $text;
    public int $voter_count;

}
