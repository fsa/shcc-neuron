<?php

namespace Auth;

interface UserInterface {
    
    function getLogin(): ?string;

    function getName(): string;

    function getEmail(): string;

    function getScope(): array;

    function stillActive(): bool;

}
