<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Database\Console\Migrations\StatusCommand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\PersonalAccessToken;
use Spatie\Permission\Models\Role;

use function PHPUnit\Framework\returnValue;

class authController extends Controller
{
    public function login(Request $request)
    {
       $validateUser =  $request->validate([
            "email" => 'required|email',
            "password"=> 'required'
        ]); 

        if(!Auth::attempt($request->only('email','password'))){
            return response()->json([
                'status'=> false ,
                'message'=> 'invalid credianls'
            ]);
        }

        $user = User::where('email',$request->email)->first();

        return response()->json([
            'status'=>true ,
            'message'=> 'user logged in succssufly !',
            'user'=> $user->load('roles') ,
            'token'=>$user->createToken("API TOKEN")->plainTextToken
        ],200);
      
    }

    public function logout(Request $request){

        auth()->user()->tokens()->delete();

        return response()->json(['message' => 'Logged out'], 200);
    }

    public function getUser($userId){
        $user = User::find($userId);
        
        if(!$user){
            return response()->json([
                'status' => false,
                'message' => 'User not found'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'message' => 'User retrieved successfully',
            'data' => $user->load('roles')
        ], 200);
    }

    public function addUser(Request $request)
    {
        $request->validate(
            [
                'name' => 'required|string|',
                'email' => 'required|email',
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
                    'status' => 200 ,
                    'message' => 'user added successfuly'
                ]
            );
        };


    }

    public function deleteUser($userId){
        $user = User::findOrFail($userId) ;
        
        
        if($user->delete()){
            return response()->json([
                'status' => true ,
                'message' => 'user delete succssfuly !' ,
            ],200);
        }

        
    }

    public function index(){
        $users = User::get()->load('roles');
        return response()->json([
            'status' => true,
            'message' => 'Users retrieved successfully',
            'data' => $users
        ], 200);
    }

    public function update(Request $request,$userId){
        $user = User::findOr($userId) ;

        $request->validate([
            "email" => 'nullable|email',
            "password" => 'nullable',
            "name" => 'nullable',
            
        ]);


    }
}
