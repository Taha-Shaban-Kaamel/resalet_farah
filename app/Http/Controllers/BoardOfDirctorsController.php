<?php

namespace App\Http\Controllers;

use App\Http\Resources\BoardOfDirctorsResource;
use App\Models\BoardOfDirctor;
use Illuminate\Http\Request;


class BoardOfDirctorsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $boardOfDirctors = BoardOfDirctor::all();
        return response()->json([
            'status' => true,
            'message' => 'Board of directors retrieved successfully',
            'data' => BoardOfDirctorsResource::collection($boardOfDirctors)
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'position' => 'required|string',
            'image' => 'required|file|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'description' => 'required|string',
            'email' => 'required|email',
            'phone' => 'required|string',
        ]);

        $data = $request->all();
        
        if($request->hasFile('image')){
            $directory = storage_path('app/public/dirctors');
            $filename = str()->random(10) . '_' . $request->file('image')->getClientOriginalName();
            $imagePath = $request->file('image')->move($directory, $filename);
            $data['image_path'] = 'dirctors/' . $filename; 
        }

        $boardOfDirctor = BoardOfDirctor::create($data);
        return response()->json([
            'status' => true,
            'message' => 'Board of director created successfully',
            'data' => new BoardOfDirctorsResource($boardOfDirctor)
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $boardOfDirctor = BoardOfDirctor::find($id);
        if (!$boardOfDirctor) {
            return response()->json([
                'status' => false,
                'message' => 'Board of director not found',
            ], 404);
        };
        return response()->json([
            'status' => true,
            'message' => 'Board of director retrieved successfully',
            'data' => new BoardOfDirctorsResource($boardOfDirctor)
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $boardOfDirctor = BoardOfDirctor::find($id);
        if (!$boardOfDirctor) {
            return response()->json([
                'status' => false,
                'message' => 'Board of director not found',
            ], 404);
        };
        $request->validate([
            'name' => 'nullable|string',
            'position' => 'nullable|string',
            'image_path' => 'nullable|string',
            'description' => 'nullable|string',
            'email' => 'nullable|email',
            'phone' => 'nullable|string',
        ]);

        $boardOfDirctor->update($request->all());
        return response()->json([
            'status' => true,
            'message' => 'Board of director updated successfully',
            'data' => new BoardOfDirctorsResource($boardOfDirctor)
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $boardOfDirctor = BoardOfDirctor::find($id);
        if (!$boardOfDirctor) {
            return response()->json([
                'status' => false,
                'message' => 'Board of director not found',
            ], 404);
        };
        $boardOfDirctor->delete();
        return response()->json([
            'status' => true,
            'message' => 'Board of director deleted successfully',
        ], 200);
    }
}
