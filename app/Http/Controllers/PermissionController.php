<?php

namespace App\Http\Controllers;

use App\Http\Requests\Permission\StorePermissionRequest;
use App\Http\Requests\Permission\UpdatePermissionRequest;
use App\Models\Permission;
use App\Services\PermissionService;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    protected $permissionService;
    public function __construct(PermissionService $permissionService)
    {
        $this->permissionService = $permissionService;
    }
    /**
     * Display a listing of the resource.
     */

    /**
     * get all  permissions
     * 
     * @param Request  $request 
     *
     * @return response  of the status of operation : permissions 
     */
    public function index(Request $request)
    {
        $name = $request->input('name');
        $permissions = $this->permissionService->allPermissions($name, false);
        return response()->json([
            'status' => 'success',
            'data' => [
                'permissions' => $permissions
            ]
        ], 200);
    }
    /**
     * Store a newly created resource in storage.
     */
    /**
     * create a new permission
     * @param StorePermissionRequest  $request 
     *
     * @return response  of the status of operation : permission 
     */
    public function store(StorePermissionRequest $request)
    {
        $categoryData = $request->validated();

        $permission = $this->permissionService->createPermission($categoryData);

        return response()->json([
            'status' => 'success',
            'data' => [
                'permission' => $permission,
            ]
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    /**
     * get a  specified permission
     * @param Permission  $permission 
     *
     * @return response  of the status of operation : permission 
     */
    public function show(Permission $permission)
    {
        $permission = $this->permissionService->onePermission($permission);

        return response()->json([
            'status' => 'success',
            'data' => [
                'permission' => $permission,
            ]
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */

    /**
     * update a  specified permission
     * @param UpdatePermissionRequest $request
     * @param Permission  $permission 
     *
     * @return response  of the status of operation : permission 
     */
    public function update(UpdatePermissionRequest $request, Permission $permission)
    {
        $permissionData = $request->validated();
        $permission = $this->permissionService->updatePermission($permission,  $permissionData);
        return response()->json([
            'status' => 'success',
            'data' => [
                'permission' => $permission
            ]
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    /**
     * delete a  specified permission
     * @param Permission  $permission 
     *
     * @return response  of the status of operation 
     */
    public function destroy(Permission $permission)
    {
        $this->permissionService->deletePermission($permission);
        return response()->json(status: 204);
    }

    /**
     * show all  deleted permissions
     *
     * @param Request $request 
     *
     * @return response  of the status of operation : and the permissions
     */
    public function deletedPermissions(Request $request)
    {
        $name = $request->input('name');

        $permissions = $this->permissionService->allPermissions($name, true);

        return response()->json([
            'status' => 'success',
            'data' => [
                'permissions' => $permissions
            ]
        ], 200);
    }

    /**
     * restore a  permission
     *
     * @param int $permission_id 
     *
     * @return response  of the status of operation and the permission
     */
    public function restorePermission($permission_id)
    {
        $permission = $this->permissionService->restorePermission($permission_id);
        return response()->json([
            'status' => 'success',
            'data' => [
                'permission' => $permission
            ]
        ], 200);
    }
    /**
     * force delete a permission
     * 
     * @param int $permission_id 
     *
     * @return response  of the status of operation 
     */

    public function forceDeletePermission($permission_id)
    {
        $this->permissionService->forceDeletePermission($permission_id);
        return response()->json(status: 204);
    }
}
