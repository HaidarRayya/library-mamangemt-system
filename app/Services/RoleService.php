<?php

namespace App\Services;

use App\Enums\UserRole;
use App\Http\Resources\RoleResource;
use App\Http\Resources\PermissionResource;
use App\Models\Permission;
use Exception;
use Illuminate\Support\Facades\Log;
use App\Models\Role;
use Illuminate\Http\Exceptions\HttpResponseException;

class RoleService
{
    /**
     * show all roles
     * @param string $name  
     * @return RoleResource $roles 
     */
    public function allRoles($name, $deletedRole)
    {
        try {
            if ($deletedRole) {
                $roles = Role::onlyTrashed()->notAdminRole();
            } else {
                $roles = Role::notAdminRole();
            }
            $roles = $roles
                ->byName($name)
                ->get();
            $roles = RoleResource::collection($roles);
            return  $roles;
        } catch (Exception $e) {
            Log::error("error in get all categories"  . $e->getMessage());
            throw new HttpResponseException(response()->json(
                [
                    'status' => 'error',
                    'message' => "there is something wrong in server",
                ],
                500
            ));
        }
    }
    /**
     * show a category and all  her permissions
     * @param  Category $category  
     * @return array CategoryResource $category and BookResource $books
     */
    public function oneRole($role)
    {

        if ($role->name == UserRole::ADMIN->value) {
            throw new HttpResponseException(response()->json(
                [
                    'status' => 'error',
                    'message' => "لا يمكنك القيام بهذه العملية",
                ],
                422
            ));
        }
        try {
            $permissions = $role->load('permissions')->permissions;
            $role = RoleResource::make($role);

            $permissions = $permissions->isNotEmpty() ? PermissionResource::collection($permissions) : [];

            return [
                'role' => $role,
                'permissions' =>  $permissions
            ];
        } catch (Exception $e) {
            Log::error("error in  show a  role"  . $e->getMessage());
            throw new HttpResponseException(response()->json(
                [
                    'status' => 'error',
                    'message' => "there is something wrong in server",
                ],
                500
            ));
        }
    }
    /**
     * create a  new category
     * @param  array $roleData  
     * @return RoleResource book  
     */
    public function createRole($roleData)
    {
        $role = Role::create($roleData);
        $role  = RoleResource::make($role);
        return  $role;
        try {
        } catch (Exception $e) {
            Log::error("error in create a  role"  . $e->getMessage());
            throw new HttpResponseException(response()->json(
                [
                    'status' => 'error',
                    'message' => "there is something wrong in server",
                ],
                500
            ));
        }
    }
    /**
     * update a role
     * @param Role $role  
     * @param  array $roleData  
     * @return RoleResource role  
     */
    public function updateRole(Role $role, $roleData)
    {

        try {
            $role->update($roleData);
            $role = RoleResource::make(Role::find($role->id));
            return  $role;
        } catch (Exception $e) {
            Log::error("error in   update a  category"  . $e->getMessage());
            throw new HttpResponseException(response()->json(
                [
                    'status' => 'error',
                    'message' => "there is something wrong in server",
                ],
                500
            ));
        }
    }

    /**
     *  delete a  role
     * @param Role $role  
     */
    public function deleteRole(Role $role)
    {
        if ($role->name == UserRole::ADMIN->value) {
            throw new HttpResponseException(response()->json(
                [
                    'status' => 'error',
                    'message' => "لا يمكنك القيام بهذه العملية",
                ],
                422
            ));
        }
        try {
            $role->delete();
        } catch (Exception $e) {
            Log::error("error in  soft delete a  role"  . $e->getMessage());
            throw new HttpResponseException(response()->json(
                [
                    'status' => 'error',
                    'message' => "there is something wrong in server",
                ],
                500
            ));
        }
    }


    /**
     * restore a book
     * @param int $category_id      
     * @return CategoryResource $category
     */
    public function restoreRole($role_id)
    {
        try {
            $role = Role::withTrashed()->find($role_id);
            $role->restore();
            return RoleResource::make($role);
        } catch (Exception $e) {
            Log::error("error in restore a category"  . $e->getMessage());
            throw new HttpResponseException(response()->json(
                [
                    'status' => 'error',
                    'message' => "there is something wrong in server",
                ],
                500
            ));
        }
    }

    /**
     * delete a  book
     * @param Category $category  
     */
    public function forceDeleteRole($role_id)
    {
        $role = Role::withTrashed()->findOrFail($role_id);
        if ($role->name == UserRole::ADMIN->value) {
            throw new HttpResponseException(response()->json(
                [
                    'status' => 'error',
                    'message' => "لا يمكنك القيام بهذه العملية",
                ],
                422
            ));
        }
        try {
            $role->forceDelete();
        } catch (Exception $e) {
            Log::error("error in delete a  category"  . $e->getMessage());
            throw new HttpResponseException(response()->json(
                [
                    'status' => 'error',
                    'message' => "there is something wrong in server",
                ],
                500
            ));
        }
    }

    public function addPermissionToRole($role, $permissionsData)
    {
        if ($role->name == UserRole::ADMIN->value) {
            throw new HttpResponseException(response()->json(
                [
                    'status' => 'error',
                    'message' => "لا يمكنك القيام بهذه العملية",
                ],
                422
            ));
        }
        try {
            $permissionsData = array_unique(array_filter($permissionsData, function ($num) {
                return $num > 0;
            }));

            foreach ($permissionsData as $i) {
                $role->permissions()->attach(Permission::find($i));
            }
        } catch (Exception $e) {
            Log::error("error in delete a  category"  . $e->getMessage());
            throw new HttpResponseException(response()->json(
                [
                    'status' => 'error',
                    'message' => "there is something wrong in server",
                ],
                500
            ));
        }
    }

    public function removePermissionFromRole($role, $permissionsData)
    {
        if ($role->name == UserRole::ADMIN->value) {
            throw new HttpResponseException(response()->json(
                [
                    'status' => 'error',
                    'message' => "لا يمكنك القيام بهذه العملية",
                ],
                422
            ));
        }
        try {
            $permissionsData = array_unique(array_filter($permissionsData, function ($num) {
                return $num > 0;
            }));
            foreach ($permissionsData as $i) {
                $role->permissions()->detach(Permission::find($i));
            }
        } catch (Exception $e) {
            Log::error("error in delete a  category"  . $e->getMessage());
            throw new HttpResponseException(response()->json(
                [
                    'status' => 'error',
                    'message' => "there is something wrong in server",
                ],
                500
            ));
        }
    }
}
