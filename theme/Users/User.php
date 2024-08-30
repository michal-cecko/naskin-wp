<?php

namespace Theme\Users;

use Saurus\App\Modules\Wordpress\Models\User as SaurusUser;
use Saurus\App\Modules\Log\ILoggable;

class User extends SaurusUser implements ILoggable {
    public function getLogLinkAttribute(): ?string
    {
        return $this->edit_link;
    }

    public function getLogTitleAttribute(): string
    {
        return $this->display_name;
    }
}