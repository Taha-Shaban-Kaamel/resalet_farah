<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\RolesResource;
use App\Http\Resources\PermissionsResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesPermissionController extends Controller
{
    /**
     * Display a listing of roles.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $roles = Role::with('permissions')->latest()->paginate(10);
        $permissions = Permission::all();

        return response()->json([
            'status' => true,
            'message' => 'Roles retrived succssfuly',
            'roles' => RolesResource::collection($roles),
            'permissions' => $permissions
        ]);
    }

    /**
     * Store a newly created role in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ], [
            'name.unique' => 'the role is already exist'
        ]);

        try {
            DB::beginTransaction();

            $role = Role::create([
                'name' => $validated['name'],
                'guard_name' => 'web'
            ]);

            if (isset($validated['permissions'])) {
                $permissions = Permission::whereIn('id', $validated['permissions'])->pluck('name');
                $role->syncPermissions($permissions);
            }

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'role created succssfuly !',
                'role' => new RolesResource($role)
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'something went wrong !',
            ], 422);
        }
    }

    /**
     * Update the specified role in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $role = Role::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $role->id,
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        try {
            DB::beginTransaction();

            $role->update(['name' => $validated['name']]);

            if (isset($validated['permissions'])) {
                $permissions = Permission::whereIn('id', $validated['permissions'])->pluck('name');
                $role->syncPermissions($permissions);
            } else {
                $role->syncPermissions([]);
            }

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'role updated succssfuly!',
                'role' => new RolesResource($role)
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Failed to update role'
            ]);
        }
    }

    /**
     * Remove the specified role from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $role = Role::findById($id,'web');


        if ($role->name === 'super admin') {
            return response()->json([
                'status' => false,
                'message' => "you can't delete super admin role"
            ]);
        }

        try {
            DB::beginTransaction();

            $role->syncPermissions([]);

            $role->delete();

            DB::commit();

            return response()->json(
                [
                    'status' => true,
                    'message' => 'role deleted !'
                ]
            );
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Failed to delete role '
            ]);
        }
    }

    /**
     * Display a listing of permissions.
     *
     * @return \Illuminate\Http\Response
     */
    public function permissionsIndex()
    {
        $permissions = PermissionsResource::collection(Permission::latest()->paginate(10));
        return response()->json([
            'status' => true,
            'message' => 'Permissions retrived succssfuly',
            'permissions' => $permissions
        ]);
    }

    /**
     * Store a newly created permission in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function permissionStore(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:permissions,name',
        ]);

        try {
           $permission = Permission::create([
                'name' => $validated['name'],
                'guard_name' => 'web',
            ]);

            return response()->json([
                'status' => true ,
                'message' => 'permission stored succssfuly !',
                'permission'=> new PermissionsResource($permission) 
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false ,
                'message' => 'filed to store permission '
            ]);
        }
    }

    /**
     * Update the specified permission in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function permissionUpdate(Request $request, $id)
    {
        $permission = Permission::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:permissions,name,' . $permission->id,
        ]);

        try {
            $permission->update([
                'name' => $validated['name']
            ]);

            return response()->json([
                'status' => true ,
                'message' => 'permission updated succssfuly!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false ,
                'message' => 'falied update permission '
            ]);
        }
    }

    /**
     * Remove the specified permission from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function permissionDestroy($id)
    {
        $permission = Permission::findOrFail($id);

        try {
            // Check if permission is being used by any role
            if ($permission->roles()->count() > 0) {
                return response()->json([
                    'status' => false ,
                    'message' => "can't delete this permission casue it's assigned to one or more role !"
                ],422);
            }

            $permission->delete();

            return response()->json([
                "status" => true ,
                "message" => 'permission deleted succssfuly !'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false , 
                'message' => "failed delete permission"
            ]);
        }
    }
}
