<?php

/**
 * Telegram Bot API 5.0
 */

namespace Telegram\Entity;

class Venue extends AbstractEntity {

    public Location $location;
    public string $title;
    public string $address;
    public ?string $forsquare_id=null;
    public ?string $forsquare_type=null;
    public ?string $google_place_id=null;
    public ?string $google_place_type=null;

}
