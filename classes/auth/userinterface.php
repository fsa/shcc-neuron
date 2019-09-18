<?php

namespace Auth;

interface UserInterface {
    
    function getId(): ?int;

    function getLogin(): string;

    function getName(): string;

    function getEmail(): string;

    function getScope(): array;
    
    static function jsonUnserialize($json);
}
