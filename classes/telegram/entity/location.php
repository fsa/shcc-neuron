<?php

/**
 * Telegram Bot API 5.0
 */

namespace Telegram\Entity;

class Location extends AbstractEntity {

    public float $longitude;
    public float $latitude;
    public ?float $horizontal_accuracy=null;
    public ?int $live_period=null;
    public ?int $heading=null;
    public ?int $proximity_alert_radius=null;

}
