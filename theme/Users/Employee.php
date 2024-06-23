<?php

namespace Theme\Users;

use Theme\PostTypes\Service;

class Employee extends User
{
    public function getMutualCalendarBlockingEmployeesAttribute(): iterable
    {
        return get_field("blocking-calendar", $this->acf_id) ?? [];
    }

    public function getAllowedServiceIdsAttribute(): iterable
    {
        return get_field("services", $this->acf_id) ?? [];
    }

    public function getAllowedServicesAttribute(): iterable
    {
        return Service::whereIn("id", get_field("services", $this->acf_id) ?? [])->get()->append(['price', 'duration']);
    }

    public function getVacationColorAttribute() {
        return get_field("vacation_color", $this->acf_id);
    }

    public function getProfilePictureAttribute() : ?string {
        return get_field("profile_image", $this->acf_id);
    }

    public function getWorktimeAttribute() : array {
        return get_field("worktime", $this->acf_id);
    }

    public function getLunchtimeAttribute() : array {
        return get_field("lunchtime", $this->acf_id);
    }

    public static function getRole(): ?string
    {
        return "employee";
    }
}