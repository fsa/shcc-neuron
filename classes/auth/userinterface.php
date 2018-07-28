<?php

namespace Auth;

interface UserInterface {
    
    function getId();

    function getLogin(): string;

    function getName(): string;

    function getEmail(): string;

    function getGroups(): array;
}
