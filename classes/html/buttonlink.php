<?php

namespace HTML;

class ButtonLink{

    private $name;
    private $link;
    private $field;

    public function __construct(string $name, string $link, string $field='id') {
        $this->name=$name;
        $this->link=$link;
        $this->field=$field;
    }

    public function getParamField(): string {
        return $this->field;
    }

    public function getHtml($value): string {
        return sprintf('<a href="'.$this->link.'">%s</a>',$value,$this->name);
    }
}