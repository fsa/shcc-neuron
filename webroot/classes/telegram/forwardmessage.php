<?php

/**
 * Telegram Bot API 5.0
 */

namespace Telegram;

class ForwardMessage extends Query {

    public $chat_id;
    public $from_chat_id;
    public $disable_notification;
    public $message_id;

    public function __construct(string $chat_id=null, string $from_chat_id=null, int $message_id=null, bool $disable_notification=null) {
        if (isset($chat_id)) {
            $this->setChatId($chat_id);
        }
        if (isset($from_chat_id)) {
            $this->setFromChatId($from_chat_id);
        }
        if (isset($message_id)) {
            $this->setMessageId($message_id);
        }
        if (isset($disable_notification)) {
            $this->setDisableNotification($disable_notification);
        }
    }

    public function setChatId(string $id): void {
        $this->chat_id=$id;
    }

    public function setFromChatId(string $id): void {
        $this->from_chat_id=$id;
    }

    public function setDisableNotification(bool $bool=true): void {
        $this->disable_notification=$bool;
    }

    public function setMessageId(int $id) {
        $this->message_id=$id;
    }

    public function buildQuery(): array {
        if (is_null($this->chat_id) or is_null($this->from_chat_id) or is_null($this->message_id)) {
            throw new Exception('Required: chat_id, from_chat_id, message_id');
        }
        return parent::buildQuery();
    }

}
