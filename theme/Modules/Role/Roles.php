<?php

namespace Theme\Modules\Role;

use Exception;
use Saurus\App\Main;
use Theme\Enum\User\Role;
use Theme\Users\Admin;
use Theme\Users\Employee;
use Theme\Users\Manager;
use Theme\Users\Owner;
use Theme\Users\TogetherEmployee;
use WP_User;

class Roles
{
    public array $roles = [];

    public function __construct()
    {
        $this->addRole(__("Administrátor"), Role::ADMIN, AdminRole::class, Admin::class);
        $this->addRole(__("Pracovník"), Role::EMPLOYEE, EmployeeRole::class, Employee::class);
        $this->addRole(__("Spoločný pracovník"), Role::TOGETHER_EMPLOYEE, TogetherEmployeeRole::class, TogetherEmployee::class);
        $this->addRole(__("Manažér"), Role::MANAGER, ManagerRole::class, Manager::class);
        $this->addRole(__("Majiteľ"), Role::OWNER, OwnerRole::class, Owner::class);
    }

    private function addRole(string $name, Role $role, string $roleModelClass, string $roleUserModelClass): void
    {
        $roleData = [
            'name' => $name,
            'module_class' => $roleModelClass,
            'module' => Main::initModule(new $roleModelClass()),
            'model' => $roleUserModelClass,
            'enum' => $role,
            'capabilities' => $roleModelClass::capabilities(),
        ];

        $this->roles[$role->value] = $roleData;
    }

    /**
     * @return void
     */
    public function register(): void
    {
        foreach ($this->roles as $roleKey => $roleData) {
            $role = get_role($roleKey);
            if (!$role) {
                add_role($roleData['enum']->value, $roleData['name'], $roleData['capabilities']);
                return;
            }

            // If custom role, delete before adding
            if (!in_array(strtolower($roleKey), ['administrator', 'editor', 'author', 'contributor', 'subscriber'])) {
                foreach ($role->capabilities as $cap => $grant) {
                    $role->remove_cap($cap);
                }
            }

            foreach ($roleData['capabilities'] as $cap => $value) {
                $role->add_cap($cap, $value);
            }

        }
    }

    /**
     * @filter editable_roles
     * @param $all_roles
     * @return mixed
     */
    public function restrict_editable_roles($all_roles): mixed
    {
        $role = wp_get_current_user()->roles[0];

        if (in_array($role, [Role::MANAGER->value, Role::OWNER->value])) {
            unset($all_roles[Role::ADMIN->value], $all_roles[Role::OWNER->value]);
        }

        if ($role == Role::MANAGER->value) {
            unset($all_roles[Role::ADMIN->value], $all_roles[Role::OWNER->value], $all_roles[Role::MANAGER->value]);
        }

        return $all_roles;
    }

    /**
     * Forbid all roles except manager, owner and admin from switching users
     *
     * @filter user_has_cap 9 4
     * @param array   $allcaps
     * @param array   $caps
     * @param array   $args
     * @param WP_User $user
     * @return array
     */
    public function forbid_user_switching_plugin(array $allcaps, array $caps, array $args, WP_User $user): array
    {
        if ('switch_to_user' === $args[0]) {
            $role = $user->roles[0];

            if (!in_array($role, [Role::MANAGER->value, Role::OWNER->value, Role::ADMIN->value])) {
                $allcaps['switch_users'] = false;
            }
        }

        return $allcaps;
    }

    /**
     * @action switch_to_user
     * @param int    $user_id
     * @param int    $old_user_id
     * @param string $new_token
     * @param string $old_token
     * @return void
     */
    public function restrict_user_switching_plugin(int $user_id, int $old_user_id, string $new_token, string $old_token): void
    {
        $userTarget = get_user_by('id', $user_id);
        $userTargetRole = $userTarget->roles[0];

        $userSource = get_user_by('id', $old_user_id);
        $userSourceRole = $userSource->roles[0];

        if ($userSourceRole === Role::MANAGER->value && in_array($userTargetRole, [Role::ADMIN->value, Role::OWNER->value])) {
            switch_off_user();
        }

        if ($userSourceRole === Role::OWNER->value && in_array($userTargetRole, [Role::ADMIN->value])) {
            switch_off_user();
        }
    }

    public function getRoleEnumByRoleClass(string $roleClass): ?Role
    {
        foreach ($this->roles as $roleData) {
            if ($roleData['module_class'] === $roleClass) {
                return $roleData['enum'];
            }
        }

        return null;
    }

    /**
     * @action acf/save_post 20
     *
     * @param $post_id
     * @return void
     * @throws Exception
     */
    public function update_user_acf_fields_action($post_id): void
    {
        if (str_starts_with($post_id, 'user_')) {
            $user_id = str_replace('user_', '', $post_id);

            $employees = Employee::all();
            if ($employee = $employees->where("ID", $user_id)->first()) {
                $mutuals = implode(", ", array_map(fn($mutual) => $employees->where("ID", $mutual)->first()?->first_name, $employee->mutual_calendar_blocking_employees));
                $allowedServices = $employee->allowed_services->map(fn($service) => $service->title)->implode(", ");

                main()->log()->warningDB("Aktualizovaný pracovník {$employee->first_name}. Aktuálne dáta sú - Spoločný kalendár: {$mutuals} - Služby ktoré vykonáva: {$allowedServices} - Pracovný čas: od {$employee->worktime['start']} do {$employee->worktime['end']} - Prestávka: od " . (!empty($start = $employee->lunchtime['start']) ? $start : "neurčené") . " do " . (!empty($end = $employee->lunchtime['end']) ? $end : "neurčené"), null, [$employee]);
            }
        }
    }
}