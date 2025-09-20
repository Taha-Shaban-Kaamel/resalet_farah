<?php

namespace App\Http\Controllers\admin;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Database\Console\Migrations\StatusCommand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\PersonalAccessToken;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class authController extends Controller
{
    public function login(Request $request)
    {
        $validateUser = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'status' => false,
                'message' => 'invalid credianls'
            ], 401);
        }

        $user = User::where('email', $request->email)->first();
        $token = $user->createToken('API TOKEN')->plainTextToken;

        return response()->json([
            'status' => true,
            'message' => 'Welcome Back !',
            'user' => new UserResource($user->load('roles')),
            'token' => $token
        ], 200);
    }

    public function logout(Request $request)
    {
        auth()->user()->tokens()->delete();

        return response()->json(['message' => 'Logged out'], 200);
    }

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
            'data' => new UserResource($user->load('roles'))
        ], 200);
    }

    public function addUser(Request $request)
    {
        $request->validate(
            [
                'name' => 'required|string|',
                'email' => 'required|email|unique:users,email',
                'password' => 'nullable|string',
                'role' => 'required|string|exists:roles,name'
            ]
        );

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password,
        ]);
        // $role = Role::where('id',$request->role)
        // ->where('guard_name','Web')
        // ->firstOrFail();

        $user->assignRole($request->role);

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
        $users = User::get()->load('roles');
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
                'role' => 'nullable|array',
                'role.*' => 'string|exists:roles,name'
            ]);
    
            DB::beginTransaction();
    
            // Update user data
            $user->update($validatedData);
    
            // Handle role assignment if provided
            if (isset($validatedData['role'])) {
                $user->syncRoles($validatedData['role']);
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
