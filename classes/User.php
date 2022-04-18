<?php

class User extends FSA\Neuron\User
{

    public static function validate(array $properties): self
    {
        $user = new self(App::sql());
        return $user->refresh($properties) ? $user : null;
    }
}
