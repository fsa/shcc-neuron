<?php

/**
 * Telegram Bot API 5.0
 */

namespace Telegram\Entity;

class ChatPhoto extends AbstractEntity {

    public string $small_file_id;
    public string $small_file_unique_id;
    public string $big_file_id;
    public string $big_file_unique_id;

}
