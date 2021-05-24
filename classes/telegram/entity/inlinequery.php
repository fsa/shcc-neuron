<?php

/**
 * Telegram Bot API 5.0
 */

namespace Telegram\Entity;

class InlineQuery extends AbstractEntity {

    public string $id;
    public User $from;
    public ?Location $location=null;
    public string $query;
    public string $offset;

}
