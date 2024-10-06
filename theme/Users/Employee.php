<?php

namespace Theme\Users;

use Theme\Enum\User\Role;
use Theme\PostTypes\Service;

class Employee extends User
{
    public function getMutualCalendarBlockingEmployeesAttribute(): iterable
    {
        $value = get_field("blocking-calendar", $this->acf_id) ?? [];
        return empty($value) ? [] : $value;
    }

    public function getAllowedServiceIdsAttribute(): iterable
    {
        $serviceIDs = get_field("services", $this->acf_id);
        return !empty($serviceIDs) ? $serviceIDs : [];
    }

    public function getAllowedServicesAttribute(): iterable
    {
        $serviceIDs = get_field("services", $this->acf_id);
        if(empty($serviceIDs)) {
            return collect([]);
        }

        $services = Service::whereIn("id", $serviceIDs)->get()->append(['price', 'duration']);
        return $services;
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

    public static function getRole(): ?Role
    {
        return Role::EMPLOYEE;
    }
}