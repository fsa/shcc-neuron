<?php

/**
 * Telegram Bot API 5.0
 */

namespace Telegram\Entity;

class StickerSet extends AbstractEntity {

    public string $name;
    public string $title;
    public bool $is_animated;
    public bool $contains_masks;
    public array $stickers;
    public ?PhotoSize $thumb;

    protected function parseArray($key, $value) {
        $result=[];
        switch ($key) {
            case 'thumb':
                foreach ($value as $entity) {
                    $result[]=new Sticker($entity);
                }
                break;
        }
        return $result;
    }

}
