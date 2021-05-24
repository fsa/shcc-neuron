<?php

/**
 * Telegram Bot API 5.0
 */

namespace Telegram\Entity;

class PhotoSize extends AbstractEntity {

    public string $file_id;
    public string $file_unique_id;
    public int $width;
    public int $height;
    public ?int $file_size=null;

}
