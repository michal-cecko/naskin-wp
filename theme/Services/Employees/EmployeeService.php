<?php

namespace Theme\Services\Employees;

use Illuminate\Support\Collection;
use Theme\Users\Employee;

class EmployeeService
{
    public static function getEmployeesWithServices($services) : Collection {
        // Get all employees
        $employees = Employee::all();

        // Extract the IDs from the services collection
        $serviceIds = $services->pluck('id');

        // Filter employees
        $filteredEmployees = $employees->filter(function ($employee) use ($serviceIds) {
            // Extract the IDs from the employee's allowed_services
            $allowedServiceIds = collect($employee->allowed_services)->pluck('id');

            // Check if all service IDs are in allowed_service IDs
            $allServicesAllowed = $serviceIds->every(function ($serviceId) use ($allowedServiceIds) {
                return $allowedServiceIds->contains($serviceId);
            });

            return $allServicesAllowed;
        });

        return $filteredEmployees->values(); // Return the filtered collection
    }

}