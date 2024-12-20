<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of categories.
     */
    public function index(Request $request)
    {
        $authUser = $request->user();

        // Check if the user is an admin or associated with an admin
        $categories = Category::where('admin_id', $authUser->admin_id ?? $authUser->id)->get();

        return response()->json($categories, 200);
    }

    /**
     * Store a newly created category in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:50',
        ]);

        $authUser = $request->user();

        // Determine the admin_id to associate with the category
        $adminId = $authUser->admin_id ?? $authUser->id;

        $category = Category::create([
            'name' => $validatedData['name'],
            'admin_id' => $adminId,
        ]);

        return response()->json(['response' => 'success', 'category' => $category], 201);
    }

    /**
     * Display the specified category.
     */
    public function show(Request $request, string $id)
    {
        $authUser = $request->user();

        $category = Category::where('id', $id)
            ->where('admin_id', $authUser->admin_id ?? $authUser->id)
            ->first();

        if ($category) {
            return response()->json($category, 200);
        }

        return response()->json(['response' => 'No records found or unauthorized!'], 403);
    }

    /**
     * Update the specified category in storage.
     */
    public function update(Request $request, string $id)
    {
        $authUser = $request->user();

        $category = Category::where('id', $id)
            ->where('admin_id', $authUser->admin_id ?? $authUser->id)
            ->first();

        if ($category) {
            $validatedData = $request->validate([
                'name' => 'required|string|max:50',
            ]);

            $category->update($validatedData);

            return response()->json(['response' => 'success', 'category' => $category], 200);
        }

        return response()->json(['response' => 'No records found or unauthorized!'], 403);
    }

    /**
     * Remove the specified category from storage.
     */
    public function destroy(Request $request, string $id)
    {
        $authUser = $request->user();

        $category = Category::where('id', $id)
            ->where('admin_id', $authUser->admin_id ?? $authUser->id)
            ->first();

        if ($category) {
            $category->delete();
            return response()->json(['response' => 'success'], 200);
        }

        return response()->json(['response' => 'No records found or unauthorized!'], 403);
    }

    /**
     * Search for categories by name.
     */
    public function search(Request $request)
    {
        $searchTerm = $request->input('search');

        if (empty($searchTerm)) {
            return response()->json(['error' => 'Search term is required'], 400);
        }

        $authUser = $request->user();

        $categories = Category::where('name', 'like', '%' . $searchTerm . '%')
            ->where('admin_id', $authUser->admin_id ?? $authUser->id)
            ->get();

        if ($categories->isNotEmpty()) {
            return response()->json($categories, 200);
        }

        return response()->json(['response' => 'No records found!'], 404);
    }
}
