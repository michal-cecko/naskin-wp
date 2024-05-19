<?php

namespace Theme\Users;

use Saurus\App\Modules\Wordpress\Models\User as SaurusUser;

class User extends SaurusUser {
    public static function getRole(): ?string
    {
        return null;
    }
}