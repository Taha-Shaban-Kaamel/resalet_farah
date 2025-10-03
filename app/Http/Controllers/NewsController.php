<?php

namespace App\Http\Controllers;

use App\Models\News;
use Illuminate\Http\Request;
use App\Http\Resources\NewsResource;

class NewsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $news = News::all();
        return response()->json([
            'status' => true,
            'message' => 'News retrieved successfully',
            'data' => NewsResource::collection($news)
        ], 200);
    }



    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validateData = $request->validate([
            'image' => 'required|file|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        if ($validateData['image']) {
            $image = $request->file('image');
            $image_name = time() . '.' . $image->getClientOriginalExtension();
            $image->move(storage_path('app/public/images/news'), $image_name);
            $request['thumbnail'] = $image_name;
        }

        $news = News::create($request->except('image'));
        return response()->json([
            'status' => true,
            'message' => 'News created successfully',
            'data' => new NewsResource($news)
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $news = News::find($id);
        if (!$news) {
            return response()->json([
                'status' => false,
                'message' => 'News not found',
            ], 404);
        }

        return response()->json([
            'status' => true,
            'message' => 'News retrieved successfully',
            'data' => new NewsResource($news)
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $validateData = $request->validate([
            'image' => 'required|file|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $news = News::find($id);
        if (!$news) {
            return response()->json([
                'status' => false,
                'message' => 'News not found',
            ], 404);
        }

        if ($validateData['image']) {
            $image = $request->file('image');
            $image_name = time() . '.' . $image->getClientOriginalExtension();
            $image->move(storage_path('app/public/images/news'), $image_name);
            $request['thumbnail'] = $image_name;
        }

        $news->update($request->except('image'));
        return response()->json([
            'status' => true,
            'message' => 'News updated successfully',
            'data' => new NewsResource($news)
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $news = News::find($id);
        if (!$news) {
            return response()->json([
                'status' => false,
                'message' => 'News not found',
            ], 404);
        }

        $news->delete();
        return response()->json([
            'status' => true,
            'message' => 'News deleted successfully',
        ], 200);
    }
}
