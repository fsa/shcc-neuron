<?php

/**
 * Telegram Bot API 5.0
 */

namespace Telegram\Entity;

class UserProfilePhotos extends AbstractEntity {

    public int $total_count;
    public array $photos;

    protected function parseArray($key, $value) {
        if($key!='photos') {
            return $value;
        }
        $result=[];
        $i=0;
        foreach ($value as $row) {
            foreach ($row as $entity) {
                $result[i][]=new PhotoSize($entity);
            }
            $i++;
        }
        return $result;
    }

}
