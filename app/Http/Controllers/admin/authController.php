<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\ForgetPassword;

class authController extends Controller
{
    public function login(Request $request)
    {
        $validateUser = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User not found please contact admin!'
            ], 404);
        };

        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'status' => false,
                'message' => 'email or password is incorrect'
            ], 401);
        }

        $token = $user->createToken('API TOKEN')->plainTextToken;
        $user->load('roles');
        return response()->json([
            'status' => true,
            'message' => 'Welcome Back !',
            'user' => new UserResource($user),
            'token' => $token
        ], 200);
    }

    public function logout(Request $request)
    {
        auth()->user()->tokens()->delete();

        return response()->json(['message' => 'Logged out'], 200);
    }

    public function me()
    {
        $user = auth()->user();
        return response()->json([
            'status' => true,
            'message' => 'User retrieved successfully',
            'data' => new UserResource($user->load('roles'))
        ], 200);
    }

    public function updateProfile(Request $request)
    {
        $user = auth()->user();

        $request = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'title' => 'nullable|string',
            'phone' => 'nullable|string',
            'address' => 'nullable|string',
            'image' => 'nullable|file|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'password' => 'nullable|string|min:8|confirmed',
        ]);
        if ($request['password']) {
            $user->password = bcrypt($request['password']);
        }

        if ($request['image']) {
            $old = $user->image;
            if ($old) {
                $oldPath = storage_path('app/public/images/users/' . $old);
                if (file_exists($oldPath)) {
                    unlink($oldPath);
                }
            };
            $image = $request->file('image');
            $image_name = time() . '.' . $image->getClientOriginalExtension();
            $image->move(storage_path('app/public/images/users'), $image_name);
            $user->image = $image_name;
        }
        $user->update($request->only([
            'name',
            'email',
            'title',
            'phone',
            'password',
            'address'
        ]));
        return response()->json([
            'status' => true,
            'message' => 'User updated successfully',
            'data' => new UserResource($user->load('roles'))
        ], 200);
    }

    public function forgetPassword(Request $request)
    {
        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User not found please contact admin!'
            ], 404);
        };

        $details =[];
        $details['name'] = $user->name;
        $details['code'] = str()->random(6);
        $details['expiry'] = '60 minutes';
        $user->update([
            'forget_password_code' => $details['code']
        ]);
        
        Mail::to($user->email)->send(new ForgetPassword($details));

        return response()->json([
            'status' => true,
            'message' => 'Password reset email sent successfully',
        ], 200);
    }

    public function resetPassword(Request $request)
    {
        $validateUser = $request->validate([
            'email' => 'required|email',
            'code' => 'required',
            'password' => 'required|string|min:8|confirmed',
        ]);
        $user = User::where('email', $validateUser['email'])->first();
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User not found please contact admin!'
            ], 404);
        };


        if ($user->forget_password_code != $validateUser['code']) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid code'
            ], 400);
        }

        $user->update([
            'password' => bcrypt($validateUser['password']),
            'forget_password_code' => null
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Password reset successfully',
        ], 200);
    }
}
