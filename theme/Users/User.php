<?php

namespace Theme\Users;

use Saurus\App\Modules\Wordpress\Models\User as SaurusUser;
use Saurus\App\Modules\Log\ILoggable;

class User extends SaurusUser implements ILoggable {
    public function getEditLinkAttribute(): string
    {
        return admin_url("user-edit.php?user_id={$this->ID}");
    }

    public function getLogLinkAttribute(): ?string
    {
        return $this->edit_link;
    }

    public function getLogTitleAttribute(): string
    {
        return $this->display_name;
    }

    public static function getRole(): ?string
    {
        return null;
    }
}