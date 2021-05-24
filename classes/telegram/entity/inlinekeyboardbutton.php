<?php

/**
 * Telegram Bot API 4.9
 */

namespace Telegram\Entity;

class InlineKeyboardButton extends AbstractEntity {

    public $text;
    public $url;
    public $login_url;
    public $callback_data;
    public $switch_inline_query;
    public $switch_inline_query_current_chat;
    public $callback_game;
    public $pay;
    
    public function __construct($text) {
        $this->text=$text;
    }
    
    public function setText(string $text) {
        $this->text=$text;        
    }

    public function setUrl(string $url) {
        $this->url=$url;
    }
    
    public function setLoginUrl(LoginUrl $login_url) {
        $this->login_url=$login_url;
    }
    
    public function setCallbackData(string $callback_data) {
        $this->callback_data=$callback_data;
    }
    
    public function setSwitchInlineQuery(string $switch_inline_query) {
        $this->switch_inline_query=$switch_inline_query;
    }
    
    public function setSwitchInlineQueryCurrentChat(string $switch_inline_query_current_chat) {
        $this->switch_inline_query_current_chat=$switch_inline_query_current_chat;
    }
    
    public function setCallbackGame(CallbackGame $callback_game) {
        $this->callback_game=$callback_game;
    }
    
    public function setPay(bool $pay) {
        $this->pay=$pay;
    }

}
