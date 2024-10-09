<?php

namespace App\Services;

use App\Enums\UserPermission;
use App\Enums\UserRole;
use App\Models\Permission;
use Exception;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Log;

class ManagePermisionService
{
    /**
     * get a all permissions
     * @return array  permissions
     * 
     */

    public static function arrayPermissions()
    {
        return [
            UserRole::SALES_MANAGER->value => [
                UserPermission::ACCEPT_ORDER->value,
                UserPermission::REJECT_ORDER->value
            ],
            UserRole::DELIVERY->value => [
                UserPermission::START_ORDER->value,
                UserPermission::END_ORDER->value,
            ],
            UserRole::CUSTOMER->value => [
                UserPermission::CREATE_CART_ITEM->value,
                UserPermission::UPDATE_CART_ITEM->value,
                UserPermission::DELETE_CART_ITEM->value,
                UserPermission::CONFIRM_ORDER->value,
                UserPermission::DELETE_ORDER->value
            ]
        ];
    }
    /**
     * check if permission can assign to role 
     *  @param  array $valid_permissions
     *  @param  array $entred_permissions
     * @return string  message
     */
    public static function checkPermissions($valid_permissions, $entred_permissions)
    {
        try {
            $valid_permissions_id = Permission::whereIn('name', $valid_permissions)
                ->select('id')->get();

            $permissions_id = [];
            foreach ($valid_permissions_id as $i) {
                array_push($permissions_id, $i->id);
            }
            $message = "";
            foreach ($entred_permissions as $i) {
                if (!in_array($i, $permissions_id)) {
                    $p = Permission::find($i)->name;
                    $message .=  '\n' . " الى هذا الدور " . $p  . " لا يمكنك اضافة السماحية";
                }
            }
            return   $message;
        } catch (Exception $e) {
            Log::error("error in  register" . $e->getMessage());
            throw new HttpResponseException(response()->json(
                [
                    'status' => 'error',
                    'message' =>   "يرجى التأكد من السماحيات المدخلة"
                ],
                422
            ));
        }
    }
}
