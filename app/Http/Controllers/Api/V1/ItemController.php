<?php

namespace App\Http\Controllers\Api\V1;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Item;
class ItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $items = Item::with('categories:id,name')->get();
        return response()->json([
            'data' => $items,
            'message' => 'Items retrieved successfully'
        ], 200);
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
       $request->validate([
            'name' => 'required|string|max:255',
            'short_description' => 'nullable|string',
            'long_description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'unit' => 'nullable|string|max:50',
            'status' => 'required|in:publish,draft,pending',
            'created_by' => 'nullable|integer|exists:users,id',
            'updated_by' => 'nullable|integer|exists:users,id',
            'featured' => 'required|boolean'
        ]);

        $image = $request->file('image');
        if(!empty($image) && $image->isValid()) {
            $originalName = $image->getClientOriginalName();
            $sanitizedOriginalName = preg_replace('/[^a-zA-Z0-9_\.-]/', '_', pathinfo($originalName, PATHINFO_FILENAME));
            $extension = $image->getClientOriginalExtension();
            $originalName = $sanitizedOriginalName . '_' . uniqid() . '.' . $extension;
        }

        $imagePath = $image->storeAs('images', $originalName, 'public');

        $item = Item::create([
            'name' => $request->name,
            'short_description' => $request->short_description,
            'long_description' => $request->long_description,
            'price' => $request->price,
            'unit' => $request->unit,
            'image' => $imagePath,
            'status' => $request->status,
            'created_by' => $request->created_by ?? auth()->id(),
            'updated_by' => $request->updated_by ?? auth()->id(),
            'featured' => $request->featured,
        ]);

        $item->categories()->attach($request->categories_id);

        return response()->json([
            'message' => 'Item created successfully',
            'data' => $item,
            'image_url' => asset('storage/' . $imagePath)
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $item = Item::with('categories:id,name')->find($id);
        if (!$item) {
            return response()->json([
                'message' => 'Item not found'
            ], 404);
        }
        return response()->json([
            'data' => $item,
            'message' => 'Item retrieved successfully'
        ], 200);
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
        $item = Item::find($id);
        if (!$item) {
            return response()->json([
                'message' => 'Item not found'
            ], 404);
        }

        $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'short_description' => 'nullable|string',
            'long_description' => 'nullable|string',
            'price' => 'sometimes|required|numeric|min:0',
            'image' => 'sometimes|required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'unit' => 'nullable|string|max:50',
            'status' => 'sometimes|required|in:publish,draft,pending',
            'created_by' => 'nullable|integer|exists:users,id',
            'updated_by' => 'nullable|integer|exists:users,id',
            'featured' => 'sometimes|required|boolean'
        ]);

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            if($image->isValid()) {
                $originalName = $image->getClientOriginalName();
                $sanitizedOriginalName = preg_replace('/[^a-zA-Z0-9_\.-]/', '_', pathinfo($originalName, PATHINFO_FILENAME));
                $extension = $image->getClientOriginalExtension();
                $originalName = $sanitizedOriginalName . '_' . uniqid() . '.' . $extension;

                $imagePath = $image->storeAs('images', $originalName, 'public');
                $item->image = $imagePath;
            }
        }

        $item->update($request->only([
            'name',
            'short_description',
            'long_description',
            'price',
            'unit',
            'status',
            'created_by',
            'updated_by',
            'featured'
        ]));

        if ($request->has('categories_id')) {
            $item->categories()->sync($request->categories_id);
        }

        return response()->json([
            'message' => 'Item updated successfully',
            'data' => $item,
            'image_url' => isset($imagePath) ? asset('storage/' . $imagePath) : null
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $item = Item::find($id);
        if (!$item) {
            return response()->json([
                'message' => 'Item not found'
            ], 404);
        }

        $item->categories()->detach();
        $item->delete();

        return response()->json([
            'message' => 'Item deleted successfully'
        ], 200);
    }

     public function update_item(Request $request, string $id)
    {
        $item = Item::find($id);
        if (!$item) {
            return response()->json([
                'message' => 'Item not found'
            ], 404);
        }

        $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'short_description' => 'nullable|string',
            'long_description' => 'nullable|string',
            'price' => 'sometimes|required|numeric|min:0',
            'image' => 'sometimes|required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'unit' => 'nullable|string|max:50',
            'status' => 'sometimes|required|in:publish,draft,pending',
            'created_by' => 'nullable|integer|exists:users,id',
            'updated_by' => 'nullable|integer|exists:users,id',
            'featured' => 'sometimes|required|boolean'
        ]);

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            if($image->isValid()) {
                $originalName = $image->getClientOriginalName();
                $sanitizedOriginalName = preg_replace('/[^a-zA-Z0-9_\.-]/', '_', pathinfo($originalName, PATHINFO_FILENAME));
                $extension = $image->getClientOriginalExtension();
                $originalName = $sanitizedOriginalName . '_' . uniqid() . '.' . $extension;

                $imagePath = $image->storeAs('images', $originalName, 'public');
                $item->image = $imagePath;
            }
        }

        $item->update($request->only([
            'name',
            'short_description',
            'long_description',
            'price',
            'unit',
            'status',
            'created_by',
            'updated_by',
            'featured'
        ]));

        if ($request->has('categories_id')) {
            $item->categories()->sync($request->categories_id);
        }

        return response()->json([
            'message' => 'Item updated successfully',
            'data' => $item,
            'image_url' => isset($imagePath) ? asset('storage/' . $imagePath) : null
        ], 200);
    }
}
