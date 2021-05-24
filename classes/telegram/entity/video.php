<?php

/**
 * Telegram Bot API 5.0
 */

namespace Telegram\Entity;

class Video extends AbstractEntity {

    public string $file_id;
    public string $file_unique_id;
    public int $width;
    public int $height;
    public int $duration;
    public ?PhotoSize $thumb=null;
    public ?string $file_name=null;
    public ?string $mime_type=null;
    public ?int $file_size=null;

}
