<?php

namespace Theme\Users;

use Theme\Enum\User\Role;

class Owner extends User
{
    public static function getRole(): ?Role
    {
        return Role::OWNER;
    }
}