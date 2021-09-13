<?php

/**
 * Telegram Bot API 5.0
 */

namespace Telegram\Entity;

class ReplyKeyboardMarkup extends AbstractEntity implements ReplyMarkupInterface {

    public array $keyboard;
    public ?bool $resize_keyboard=null;
    public ?bool $one_time_keyboard=null;
    public ?bool $selective=null;
    private $row;

    public function __construct(array $buttons=null, bool $resize_keyboard=null, bool $one_time_keyboard=null, bool $selective=null) {
        if (!is_null($buttons)) {
            $this->keyboard=$buttons;
        }
        if (!is_null($resize_keyboard)) {
            $this->resize_keyboard=$resize_keyboard;
        }
        if (!is_null($one_time_keyboard)) {
            $this->one_time_keyboard=$one_time_keyboard;
        }
        if (!is_null($selective)) {
            $this->selective=$selective;
        }
        $this->row=0;
    }

    public function addButton(KeyboardButton $button) {
        $this->keyboard[$this->row][]=clone $button;
    }

    public function nextRow() {
        $this->row++;
    }

    public function setResizeKeyboard(bool $resize_keyboard=true) {
        $this->resize_keyboard=$resize_keyboard;
    }

    public function setOneTimeKeyboard(bool $one_time_keyboard=true) {
        $this->one_time_keyboard=$one_time_keyboard;
    }

    public function setSelective(bool $selective=true) {
        $this->selective=$selective;
    }

    public function __toString(): string {
        return json_encode($this->jsonSerialize(), JSON_UNESCAPED_UNICODE);
    }

    public function jsonSerialize() {
        $props=get_object_vars($this);
        unset($props['unsupported']);
        unset($props['row']);
        return array_filter($props, fn($element)=>!is_null($element));
    }

}
