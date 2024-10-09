<?php

namespace App\Rules;

use App\Enums\UserRole as EnumsUserRole;
use App\Models\User;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class CheckDelivery  implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $users = User::with('role')
            ->whereRelation('role', 'name', '=', EnumsUserRole::DELIVERY->value)
            ->select('id')
            ->get();
        $arrayOfId = [];
        foreach ($users as $user) {
            array_push($arrayOfId, $user->id);
        }
        if (!(in_array($value, $arrayOfId))) {
            $fail("المتسخدم الذي ادخلته ليس سائق");
        }
    }
}
