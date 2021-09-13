<?php

/**
 * Telegram Bot API 4.9
 */

namespace Telegram\Entity;

class KeyboardButton extends AbstractEntity {

    public string $text;
    public ?bool $request_contact=null;
    public ?bool $request_location=null;
    public ?KeyboardButtonPollType $request_poll=null;
    
    public function __construct(string $text, bool $request_contact=null, bool $request_location=null, KeyboardButtonPollType $request_poll=null) {
        $this->text=$text;
        if(isset($request_contact)) {
            $this->request_contact=$request_contact;
        }
        if(isset($request_location)) {
            $this->request_location=$request_location;
        }
        if(isset($request_poll)) {
            $this->request_poll=$request_poll;
        }
    }
    
    public function setText(string $text) {
        $this->text=$text;        
    }

    public function setRequestContact(bool $request_contact=true) {
        $this->request_contact=$request_contact;
    }
    
    public function setRequestLocation(bool $request_location=true) {
        $this->request_location=$request_location;
    }
    
    public function setRequestPoll(KeyboardButtonPollType $request_poll) {
        $this->request_poll=$request_poll;
    }

}
