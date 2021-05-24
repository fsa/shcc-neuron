<?php

/**
 * Telegram Bot API 5.0
 */

namespace Telegram\Entity;

class VideoNote extends AbstractEntity {

    public string $file_id;
    public string $file_unique_id;
    public int $length;
    public int $duration;
    public ?PhotoSize $thumb;
    public ?int $file_size;

}
