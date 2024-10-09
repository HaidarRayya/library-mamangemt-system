<?php

namespace App\Rules;

use App\Enums\UserRole;
use App\Models\Permission;
use App\Models\Role;
use App\Services\ManagePermisionService;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class checkValidPermission implements ValidationRule
{
    protected $role;
    public function __construct(Role $role)
    {
        $this->role = $role;
    }
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $message = "";
        $permissions = ManagePermisionService::arrayPermissions();
        if ($this->role->name == UserRole::SALES_MANAGER->value) {
            $message = ManagePermisionService::checkPermissions(
                $permissions[UserRole::SALES_MANAGER->value],
                array_unique(array_filter($value, function ($num) {
                    return $num > 0;
                }))
            );
        } else if ($this->role->name == UserRole::DELIVERY->value) {
            $message = ManagePermisionService::checkPermissions(
                $permissions[UserRole::DELIVERY->value],
                array_unique(array_filter($value, function ($num) {
                    return $num > 0;
                }))
            );
        } else if ($this->role->name == UserRole::CUSTOMER->value) {
            $message = ManagePermisionService::checkPermissions(
                $permissions[UserRole::CUSTOMER->value],
                array_unique(array_filter($value, function ($num) {
                    return $num > 0;
                }))
            );
        };
        if ($message != "") {
            $fail($message);
        }
    }
}
