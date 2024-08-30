<?php

namespace Theme\Users;

use Theme\Enum\User\Role;
use Theme\PostTypes\Service;

class Admin extends User
{
    public static function getRole(): ?Role
    {
        return Role::ADMIN;
    }
}