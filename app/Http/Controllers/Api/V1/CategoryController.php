<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Item;
use Illuminate\Support\Facades\Storage;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Category::where('status', 'publish')->get();
        return response()->json($categories);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:publish,draft,pending',
        ]);


        // store image and icon in to storage link
        $image= $request->file('image');
        $icon= $request->file('icon');

        if(!empty($image) && $request->file('image')->isValid()) {
            $originalName = $image->getClientOriginalName();
            $sanitizedOriginalName = preg_replace('/[^a-zA-Z0-9_\.-]/', '_', pathinfo($originalName, PATHINFO_FILENAME));
            $extension = $image->getClientOriginalExtension();
            $originalName = $sanitizedOriginalName . '_' . uniqid() . '.' . $extension;
        }

        if(!empty($icon) && $request->file('icon')->isValid()) {
            $originalName = $icon->getClientOriginalName();
            $sanitizedOriginalName = preg_replace('/[^a-zA-Z0-9_\.-]/', '_', pathinfo($originalName, PATHINFO_FILENAME));
            $extension = $icon->getClientOriginalExtension();
            $originalName = $sanitizedOriginalName . '_' . uniqid() . '.' . $extension;
        }

        $imagePath = $image->storeAs('images', $originalName, 'public');
        $iconPath = $icon->storeAs('icons', $originalName, 'public');

        $category = new Category();
        $category->name = $validatedData['name'];
        $category->slug = \Str::slug($validatedData['name']);
        $category->description = $validatedData['description'];
        $category->status = $validatedData['status'];
        $category->image = $imagePath ?? null;
        $category->icon = $iconPath ?? null;
        $category->save();

        return response()->json([
            'message' => 'Category created successfully',
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $category = Category::find($id);
        if (!$category) {
            return response()->json(['message' => 'Category not found'], 404);
        }
        return response()->json($category);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $categories = Category::find($id);
        if (!$categories) {
            return response()->json(['message' => 'Category not found'], 404);
        }
        $categories->delete();
        return response()->json(['message' => 'Category deleted successfully']);
    }

    public function update_category(Request $request, string $id){
        $validatedData = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'sometimes|required|in:publish,draft,pending',
        ]);

         $cate = Category::find($id);
        if (!$cate) {
            return response()->json(['message' => 'Category not found'], 404);
        }


        if (isset($validatedData['name'])) {
            $cate->name = $validatedData['name'];
            $cate->slug = \Str::slug($validatedData['name']);
        }
        if (isset($validatedData['description'])) {
            $cate->description = $validatedData['description'];
        }
        if (isset($validatedData['status'])) {
            $cate->status = $validatedData['status'];
        }

        // store image and icon in to storage link
        $image= $request->file('image');
        $icon= $request->file('icon');
        if(!empty($image) && $request->file('image')->isValid()) {
            $originalName = $image->getClientOriginalName();
            $sanitizedOriginalName = preg_replace('/[^a-zA-Z0-9_\.-]/', '_', pathinfo($originalName, PATHINFO_FILENAME));
            $extension = $image->getClientOriginalExtension();
            $originalName = $sanitizedOriginalName . '_' . uniqid() . '.' . $extension;
            $imagePath = $image->storeAs('images', $originalName, 'public');
            $cate->image = $imagePath;
        } elseif ($request->has('image') && $request->input('image') === null) {
            $cate->image = null;
        }

        if(!empty($icon) && $request->file('icon')->isValid()) {
            $originalName = $icon->getClientOriginalName();
            $sanitizedOriginalName = preg_replace('/[^a-zA-Z0-9_\.-]/', '_', pathinfo($originalName, PATHINFO_FILENAME));
            $extension = $icon->getClientOriginalExtension();
            $originalName = $sanitizedOriginalName . '_' . uniqid() . '.' . $extension;
            $iconPath = $icon->storeAs('icons', $originalName, 'public');
            $cate->icon = $iconPath;
        } elseif ($request->has('icon') && $request->input('icon') === null) {
            $cate->icon = null;
        }

        $cate->save();

        return response()->json([
            'message' => 'Category updated successfully',
        ], 201);
    }

    public function category_items(string $id)
    {
       $category = Category::where(['id' => $id, 'status' => 'publish'])
            ->with(['items' => function ($query) {
                $query->where('status', 'publish');
            }])
            ->first();

        if (!$category) {
            return response()->json(['message' => 'Category not found'], 404);
        }

        // Access items directly from the relationship
        $items = $category->items()
            ->where('status', 'publish')
            ->paginate(10);

        return response()->json([
            'category' => $category->name,
            'items' => $items
        ]);
    }
}
