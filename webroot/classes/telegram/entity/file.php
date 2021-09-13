<?php

/**
 * Telegram Bot API 5.0
 */

namespace Telegram\Entity;

class File extends AbstractEntity {

    public string $file_id;
    public string $file_unique_id;
    public ?int $file_size=null;
    public ?string $file_path=null;

}
