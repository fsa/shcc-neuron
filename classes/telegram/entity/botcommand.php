<?php

/**
 * Telegram Bot API 5.0
 */

namespace Telegram\Entity;

class BotCommand extends AbstractEntity {

    public string $command;
    public string $description;

}
