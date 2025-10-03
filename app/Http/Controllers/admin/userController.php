<?php

namespace App\Http\Controllers\admin;
use Illuminate\Database\Console\Migrations\StatusCommand;
use Laravel\Sanctum\PersonalAccessToken;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\UserResource;
use App\Models\User;

class userController extends Controller
{
    public function getUser($userId)
    {
        $user = User::find($userId);

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User not found'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'message' => 'User retrieved successfully',
            'data' => new UserResource($user->load('permissions'))
        ], 200);
    }

    public function addUser(Request $request)
    {
        $request->validate(
            [
                'name' => 'required|string|',
                'email' => 'required|email|unique:users,email',
                'password' => 'nullable|string',
                'permissions' => 'nullable|array|exists:permissions,name',
                'permissions.*' => 'string|exists:permissions,name',
                'title' => 'nullable|string',
                'phone' => 'nullable|string',
                'address' => 'nullable|string',
                'image' => 'nullable|file|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ],[
                'name.required' => 'Name is required',
                'email.required' => 'Email is required',
                'email.unique' => 'Email already exists',
                'password.required' => 'Password is required',
                'permissions.exists' => 'Permissions does not exist',
                'title.string' => 'Title must be a string',
                'phone.string' => 'Phone must be a string',
                'password.string' => 'Password must be a string',
                'address.string' => 'Address must be a string',
                'image.file' => 'Image must be a file',
                'image.mimes' => 'Image must be a valid image',
                'image.max' => 'Image size must not exceed 2MB',
            ]
        );
        $imgPath = null;
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->move(storage_path('app/public/images/users'), $imageName);
            $imgPath = 'images/users/' . $imageName;
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password,
            'title' => $request->title,
            'phone' => $request->phone,
            'address' => $request->address,
            'image' => $imgPath,
        ]);
  
        $user->syncPermissions($request->permissions);

        if ($user) {
            return response()->json(
                [
                    'status' => 200,
                    'message' => 'user added successfuly',
                    'user' => new UserResource($user)
                ]
            );
        };
    }

    public function deleteUser($userId)
    {
        try {
            $user = User::findOrFail($userId);
            if($user->hasRole('super admin')){
                return response()->json([
                    'status' => false,
                    'message' => 'You can not delete super admin'
                ],403);
            }
            
            $user->delete();
            return response()->json([
                'status' => true,
                'message' => 'user deleted succssfuly .'
            ],200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'status' => false,
                'message' => 'User not found!'
            ],404);
        }
    }

    public function index()
    {
        $users = User::get()->load('permissions');
        return response()->json([
            'status' => true,
            'message' => 'Users retrieved successfully',
            'data' => $users->toResourceCollection()
        ], 200);
    }

    public function updateUser(Request $request, $userId)
    {
        try {
            $user = User::findOrFail($userId);
    
            $validatedData = $request->validate([
                'email' => 'nullable|email|unique:users,email,' . $userId,
                'password' => 'nullable|string|min:6',
                'name' => 'nullable|string|max:255',
                'permissions' => 'nullable|array',
                'permissions.*' => 'string|exists:permissions,name',
                'title' => 'nullable|string',
                'phone' => 'nullable|string',
                'address' => 'nullable|string',
                'image' => 'nullable|file|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);
    
            DB::beginTransaction();
    
            // Update user data
            $user->update($validatedData);
    
            // Handle role assignment if provided
            if (isset($validatedData['permissions'])) {
                $user->syncPermissions($validatedData['permissions']);
            }
    
            DB::commit();
    
            return response()->json([
                'status' => true,
                'message' => 'User updated successfully',
                'data' => new UserResource($user->load('roles'))
            ]);
    
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Validation Error',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Failed to update user',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
