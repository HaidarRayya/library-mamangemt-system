<?php

namespace App\Rules;

use App\Enums\UserPermission as EnumsUserPermission;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class UserPermission implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $permissions = array_column(EnumsUserPermission::cases(), 'value');
        $arrayOfPermission = [];
        foreach ($permissions as $r) {
            if ($r != EnumsUserPermission::ADMIN_PERMISSIONS->value) {
                array_push($arrayOfPermission, $r);
            }
        }
        $permissions = $arrayOfPermission;
        $permissions = implode(", ", $permissions);

        if (!(in_array($value, $arrayOfPermission))) {
            $fail($permissions . " حقل :attribute  يجب ان يكون احد القيم .");
        }
    }
}
