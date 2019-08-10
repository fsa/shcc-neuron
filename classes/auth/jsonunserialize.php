<?php

namespace Auth;

trait jsonUnserialize {

    public static function jsonUnserialize($json): self {
        $user=new self;
        foreach (json_decode($json) as $key=> $value) {
            $user->$key=$value;
        }
        return $user;
    }

}
