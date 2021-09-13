<?php

/**
 * Telegram Bot API 5.0
 */

namespace Telegram\Entity;

class MaskPosition extends AbstractEntity {

    public string $point;
    public float $x_shift;
    public float $y_shift;
    public float $scale;

}
