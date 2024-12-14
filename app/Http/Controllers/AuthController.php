<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    public function adminregister(Request $request)
    {
        $validator = $request->validate([
            'name' => 'required|max:50',
            'middle_name' => 'nullable|max:20',
            'last_name' => 'required|max:20',
            'birthday' => 'required',
            'sex' => 'required',
            'company_name' => 'required|max:30',
            'address' => 'required|max:50',
            'contact_number' => 'required|min:10|max:10',
            'type' => 'required|max:10',
            'email' => 'required|email|max:255|unique:admins',
            'password' => 'required|min:8|max:16'
        ]);

        $user = Admin::create([
            'name' => $validator['name'],
            'middle_name' => $validator['middle_name'],
            'last_name' => $validator['last_name'],
            'birthday' => $validator['birthday'],
            'sex' => $validator['sex'],
            'company_name' => $validator['company_name'],
            'address' => $validator['address'],
            'contact_number' => $validator['contact_number'],
            'type' => $validator['type'],
            'email' => $validator['email'],
            'password' => bcrypt($validator['password']),
        ]);

        return response()->json(["response" => "Record has been Successfully Saved", "user" => $user ,200]);
    }

    public function adminlogin(Request $request)
    {
        $user = Admin::select("id", "email", "password")->where('email', $request->email)->first();

        if($user && Hash::check($request->password, $user->password))
        {
            $token = $user->createToken('authToken')->plainTextToken;

            return response()->json(['message'=>'successful', 'user'=>$user, 'token'=>$token], 200);
        }
        else
        {
            return response()->json(['message'=>'Failed'], 401);
        }
    }

    public function adminindex(string $id)
    {
        $admin = Admin::find($id);

        //$users = User::select("id")->where($id)->first();

        return Response(["admins" => $admin]);
    }

    public function register(Request $request)
    {
        Log::info('Register Request:', $request->all()); // Log all incoming data

        $validator = $request->validate([
            'admin_id' => 'nullable|exists:admins,id',
            'name' => 'required|max:50',
            'middle_name' => 'nullable|max:20',
            'last_name' => 'required|max:20',
            'birthday' => 'required',
            'sex' => 'required',
            'address' => 'required|max:50',
            'contact_number' => 'required|min:10|max:10',
            'type' => 'required|max:10',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|min:8|max:16',
        ]);

        $user = User::create([
            'admin_id' => $validator['admin_id'], // Nullable, validate presence
            'name' => $validator['name'],
            'middle_name' => $validator['middle_name'],
            'last_name' => $validator['last_name'],
            'birthday' => $validator['birthday'],
            'sex' => $validator['sex'],
            'address' => $validator['address'],
            'contact_number' => $validator['contact_number'],
            'type' => $validator['type'],
            'email' => $validator['email'],
            'password' => bcrypt($validator['password']),
        ]);

        Log::info('User Registered:', ['user' => $user]); // Log successful user creation

        return response()->json(["response" => "Record has been Successfully Saved", "user" => $user], 200);
    }


    public function login(Request $request)
    {
        $user = User::select("id", "name", "email", "password")->where('email', $request->email)->first();

        if($user && Hash::check($request->password, $user->password))
        {
            $token = $user->createToken('authToken')->plainTextToken;

            return response()->json(['message'=>'successful', 'user'=>$user, 'token'=>$token], 200);
        }
        else
        {
            return response()->json(['message'=>'Failed'], 401);
        }
    }

    public function index(string $id)
    {
        $user = User::find($id);

        //$users = User::select("id")->where($id)->first();

        return Response(["users" => $user]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Successfully logged out']);
    }
    public function getUsersByAdmin(Request $request)
    {
        // Ensure that the admin is logged in and retrieve their ID
        $adminId = $request->user()->id;

        // Log the admin ID to ensure it's correct
        Log::info('Logged-in admin ID: ' . $adminId);

        // Check if admin ID is valid
        if (!$adminId) {
            return response()->json(['message' => 'Admin not authenticated'], 401);
        }

        // Retrieve users associated with the logged-in admin
        $users = User::where('admin_id', $adminId)->get();

        // Log the users retrieved
        Log::info('Users retrieved for admin ' . $adminId, ['users' => $users]);

        if ($users->isEmpty()) {
            return response()->json(['message' => 'No users found for this admin'], 404);
        }

        return response()->json(['users' => $users], 200);
    }

}
