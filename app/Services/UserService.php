<?php

namespace App\Services;

use App\Enums\UserRole;
use App\Http\Resources\OrderResource;
use App\Http\Resources\UserResource;
use App\Models\Role;
use App\Models\User;
use Exception;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class UserService
{
    /**
     * get all  users
     * @param array $fillter
     * @param bool $deletedUsers
     * @return   UserResource $users
     */
    public function allUsers(array $fillter, $deletedUsers)
    {
        try {
            if ($fillter['role'] == null) {
                $op = "!=";
                $val = UserRole::ADMIN->value;
            } else {
                $op = "=";
                $val = $fillter['role'];
            }
            if ($deletedUsers) {
                $users = User::onlyTrashed()->with('role');
            } else {
                if (AuthService::user_role(Auth::user()->id) == UserRole::ADMIN->value) {
                    $users = User::with('role')
                        ->whereRelation('role', 'name', $op, $val);
                } else if (AuthService::user_role(Auth::user()->id) == UserRole::SALES_MANAGER->value) {
                    $users = User::with('role')
                        ->whereRelation('role', 'name', '=', UserRole::DELIVERY->value);
                }
            }
            $users = $users->byUserName($fillter['user_name'])->get();
            $users = UserResource::collection($users);
            return $users;
        } catch (Exception $e) {
            Log::error("error in get all users" . $e->getMessage());
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
     * create  a  new user
     * @param array $data 
     * @return   UserResource $user
     */
    public function createUser($data)
    {

        try {
            $user = new User();
            $user->name = $data['name'];
            $user->email = $data['email'];
            $user->password = $data['password'];
            $role = '';
            if ($data['role'] == "sales manager") {
                $role = Role::userRole(UserRole::SALES_MANAGER->value)->first();
            } else if ($data['role'] == "delivery") {
                $role = Role::userRole(UserRole::DELIVERY->value)->first();
            }
            $user->role_id = $role->id;
            $user->save();
            $user = UserResource::make($user);
            return $user;
        } catch (Exception $e) {
            Log::error("error in create user" . $e->getMessage());
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
     * show  a  user
     * @param User $user 
     * @return  array of  TaskResource $tasks and  UserResource $user 
     */
    public function oneUser($user)
    {
        try {
            if (AuthService::user_role($user->id) == UserRole::DELIVERY->value) {
                $user = $user->load('delivery_orders.customer');
            }
            $user = UserResource::make($user);
            $delivery_orders = OrderResource::collection($user->delivery_orders);
            return [
                'user' => $user,
                'delivery_orders' => $delivery_orders,
            ];
        } catch (Exception $e) {
            Log::error("error in get a user");
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
     * update  a  user
     * @param array $data 
     * @param User $user 
     * @return  UserResource $user
     */
    public function updateUser($data, $user)
    {
        try {
            $user->update($data);
            $user = UserResource::make($user);
            return $user;
        } catch (Exception $e) {
            Log::error("error in update a user");
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
     * delete  a user
     * @param User $user 
     */
    public function deleteUser($user)
    {
        try {
            $user->delete();
        } catch (Exception $e) {
            Log::error("error in delete a user");
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
     * restore a user
     * @param int $user_id      
     * @return UserResource $user
     */
    public function restoreUser($user_id)
    {
        try {
            $user = User::withTrashed()->find($user_id);
            $user->restore();
            return UserResource::make($user);
        } catch (Exception $e) {
            Log::error("error in restore a user");
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
     * final delelte a user
     * @param int $user_id      
     */
    public function forceDeleteUser($user_id)
    {
        try {
            $user = User::withTrashed()->find($user_id);
            $user->forceDelete();
        } catch (Exception $e) {
            Log::error("error in final delelte a user");
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
