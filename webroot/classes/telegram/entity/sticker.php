<?php

/**
 * Telegram Bot API 5.0
 */

namespace Telegram\Entity;

class Sticker extends AbstractEntity {

    public string $file_id;
    public string $file_unique_id;
    public int $width;
    public int $height;
    public bool $is_animated;
    public ?PhotoSize $thumb=null;
    public ?string $emoji=null;
    public ?string $set_name=null;
    public ?MaskPosition $mask_position=null;
    public ?int $file_size=null;

}
