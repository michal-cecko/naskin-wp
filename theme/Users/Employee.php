<?php

namespace Theme\Users;

use Theme\PostTypes\Service;

class Employee extends User
{
    public function getAllowedServicesAttribute(): iterable
    {
        $serviceIDs = get_field("services", "user_{$this->id}");

        if(empty($serviceIDs)) {
            return collect([]);
        }

        return Service::whereIn("id", $serviceIDs)->get()->append(['price', 'duration']);
    }

    public function getProfilePictureAttribute() : ?string {
        return get_field("profile_image", "user_{$this->id}");
    }

    public function getWorktimeAttribute() : array {
        return get_field("worktime", "user_{$this->id}");
    }

    public function getLunchtimeAttribute() : array {
        return get_field("lunchtime", "user_{$this->id}");
    }

    public static function getRole(): ?string
    {
        return "employee";
    }
}