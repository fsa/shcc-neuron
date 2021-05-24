<?php

/**
 * Telegram Bot API 5.0
 */

namespace Telegram\Entity;

class ProximityAlertTriggered extends AbstractEntity {

    public User $traveler;
    public User $watcher;
    public int $distance;

}
