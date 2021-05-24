<?php

/**
 * Telegram Bot API 5.0
 */

namespace Telegram;

class SendMessage extends Query {

    public $chat_id;
    public $text;
    public $parse_mode;
    public $disable_web_page_preview;
    public $disable_notification;
    public $reply_to_message_id;
    public $reply_markup;

    public function __construct(string $chat_id=null, string $text=null, string $parseMode=null) {
        if (!is_null($chat_id)) {
            $this->setChatId($chat_id);
        }
        if (!is_null($text)) {
            $this->setText($text);
        }
        switch ($parseMode) {
            case 'HTML':
                $this->setParseModeHTML();
                break;
            case 'Markdown':
            case 'MarkdownV2':
                $this->setParseModeMarkdown();
                break;
            default:
        }
    }

    public function getActionName(): string {
        return 'sendMessage';
    }

    public function setChatId($chat_id): void {
        $this->chat_id=$chat_id;
    }

    public function setText(string $text): void {
        $this->text=$this->removeHtmlEntities($text);
    }

    public function appendText(string $text): void {
        if (is_null($this->text)) {
            $this->text=$this->removeHtmlEntities($text);
        } else {
            $this->text.=$this->removeHtmlEntities($text);
        }
    }

    private function removeHtmlEntities(string $text) {
        return str_replace(['&nbsp;', '&laquo;', '&raquo;', '&quot;', '&deg;'], [' ', '«', '»', '"', '°'], $text);
    }

    public function setParseModeMarkdown(): void {
        $this->parse_mode='MarkdownV2';
    }

    public function setParseModeHTML(): void {
        $this->parse_mode='HTML';
    }

    public function setParseMode(string $parse_mode): void {
        $this->parse_mode=$parse_mode;
    }

    public function setDisableWebPagePreview(bool $bool=true): void {
        $this->disable_web_page_preview=$bool;
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
        if (is_null($this->chat_id) or is_null($this->text)) {
            throw new Exception('Required: chat_id, text');
        }
        return parent::buildQuery();
    }

}
