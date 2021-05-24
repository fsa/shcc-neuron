<?php

namespace Telegram;

class BotCommandState {
    
    const DIR='states/';

    private $state_file;
    private $command;
    private $arguments;

    public function __construct($chat_id) {
        $this->state_file=self::DIR.$chat_id.'.json';
        if (file_exists($this->state_file)) {
            $data=file_get_contents($this->state_file);
            list($this->command, $this->arguments)=unserialize($data);
        }
    }
    
    public function saveCommand($command,$params) {
        file_put_contents($this->state_file, serialize([$command,$params]));
    }
    
    public function drop() {
        unlink($this->state_file);
    }

    public function getCommand() {
        return $this->command;
    }

    public function getArguments() {
        return $this->arguments;
    }

}
