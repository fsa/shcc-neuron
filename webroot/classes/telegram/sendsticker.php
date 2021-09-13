<?php

/**
 * Telegram Bot API 5.0
 */

namespace Telegram;

class SendSticker extends Query {

    public $chat_id;
    public $sticker;
    public $disable_notification;
    public $reply_to_message_id;
    public $reply_markup;

    public function __construct(string $chat_id=null, string $sticker=null) {
        if (!is_null($chat_id)) {
            $this->setChatId($chat_id);
        }
        if (!is_null($sticker)) {
            $this->setSticker($sticker);
        }
    }

    public function getActionName(): string {
        return 'sendSticker';
    }

    public function setChatId(string $id): void {
        $this->chat_id=$id;
    }

    public function setSticker(string $sticker): void {
        $this->sticker=$sticker;
    }

    public function setDisableNotification(bool $bool=true): void {
        $this->disable_notification=$bool;
    }

    public function setReplyToMessageId(int $id): void {
        $this->reply_to_message_id=$id;
    }

    public function setReplyMarkup(Entity\ReplyMarkupInterface $keyboard): void {
        $this->reply_markup=$keyboard;
    }

    public function buildQuery(): array {
        if (is_null($this->chat_id) or is_null($this->sticker)) {
            throw new Exception('Required: chat_id, sticker');
        }
        return parent::buildQuery();
    }

}
