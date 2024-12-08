<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    /**
     * Display a listing of users for the authenticated admin.
     */
    public function index(Request $request)
    {
        $adminId = $request->user()->id; // Get the authenticated admin's ID
        $users = User::where('admin_id', $adminId)
            ->get();

        return response()->json($users, 200);
    }

    /**
     * Store a newly created user.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:50',
            'middle_name' => 'nullable|string|max:50',
            'last_name' => 'required|string|max:50',
            'birthday' => 'required|date',
            'sex' => 'required|in:M,F',
            'address' => 'required|string|max:100',
            'contact_number' => 'required|string|max:10',
            'type' => 'required|string|max:20',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
        ]);

        $user = User::create(array_merge(
            $validatedData,
            ['admin_id' => $request->user()->id] // Associate user with the admin
        ));

        Log::info('User created:', $user->toArray());

        return response()->json(['response' => 'success', 'user' => $user], 201);
    }

    /**
     * Display a specific user if owned by the authenticated admin.
     */
    public function show(Request $request, $id)
    {
        $user = User::where('id', $id)
            ->where('admin_id', $request->user()->id) // Restrict to admin's users
            ->first();

        if (!$user) {
            return response()->json(['response' => 'No records found or unauthorized!'], 403);
        }

        return response()->json($user, 200);
    }

    /**
     * Update a user if owned by the authenticated admin.
     */
    public function update(Request $request, $id)
    {
        $user = User::where('id', $id)
            ->where('admin_id', $request->user()->id) // Restrict to admin's users
            ->first();

        if (!$user) {
            return response()->json(['response' => 'No records found or unauthorized!'], 403);
        }

        $validatedData = $request->validate([
            'name' => 'sometimes|required|string|max:50',
            'middle_name' => 'nullable|string|max:50',
            'last_name' => 'sometimes|required|string|max:50',
            'birthday' => 'sometimes|required|date',
            'sex' => 'sometimes|required|in:M,F',
            'address' => 'sometimes|required|string|max:100',
            'contact_number' => 'sometimes|required|string|max:10',
            'type' => 'required|string|max:20',
            'email' => 'sometimes|required|email|unique:users,email,' . $id,
            'password' => 'sometimes|required|string|min:8',
        ]);

        $user->update($validatedData);

        return response()->json(['response' => 'success', 'user' => $user], 200);
    }

    public function destroy(Request $request, $id)
    {
        $user = User::where('id', $id)
            ->where('admin_id', $request->user()->id) // Restrict to admin's users
            ->first();

        if (!$user) {
            return response()->json(['response' => 'No records found or unauthorized!'], 403);
        }

        $user->delete();

        return response()->json(['response' => 'success'], 200);
    }
}
