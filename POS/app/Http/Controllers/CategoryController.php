<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of categories.
     */
    public function index()
    {
        $categories = Category::all();
        return response()->json($categories, 200);
    }

    /**
     * Store a newly created category in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:50', // Validation for category name
        ]);

        $category = Category::create($validatedData);

        return response()->json(['response' => 'success', 'category' => $category], 201);
    }

    /**
     * Display the specified category.
     */
    public function show(string $id)
    {
        $category = Category::find($id);

        if ($category) {
            return response()->json($category, 200);
        }

        return response()->json(['response' => 'No records found!'], 404);
    }

    /**
     * Update the specified category in storage.
     */
    public function update(Request $request, string $id)
    {
        $category = Category::find($id);

        if ($category) {
            $validatedData = $request->validate([
                'name' => 'required|string|max:50',
            ]);

            $category->update($validatedData);

            return response()->json(['response' => 'success', 'category' => $category], 200);
        }

        return response()->json(['response' => 'No records found!'], 404);
    }

    /**
     * Remove the specified category from storage.
     */
    public function destroy(string $id)
    {
        $category = Category::find($id);

        if ($category) {
            $category->delete();
            return response()->json(['response' => 'success'], 200);
        }

        return response()->json(['response' => 'No records found!'], 404);
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

        $categories = Category::where('name', 'like', '%' . $searchTerm . '%')->get();

        if ($categories->isNotEmpty()) {
            return response()->json($categories, 200);
        }

        return response()->json(['response' => 'No records found!'], 404);
    }
}
