<?php

namespace Theme\Users;

use Theme\Enum\User\Role;

class Manager extends User
{
    public static function getRole(): ?Role
    {
        return Role::MANAGER;
    }
}