<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;
use App\Http\Resources\ServiceResource;

class ServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $services = Service::all();
        return response()->json([
            'status' => true,
            'message' => 'Services retrieved successfully',
            'data' => ServiceResource::collection($services)
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validateData = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'image' => 'required|file|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($validateData['image']) {
            $image = $request->file('image');
            $image_name = time() . '.' . $image->getClientOriginalExtension();
            $image->move(storage_path('app/public/images/services'), $image_name);
            $validateData['thumbnail'] = $image_name;
        }

        $service = Service::create($validateData);
        return response()->json([
            'status' => true,
            'message' => 'Service created successfully',
            'data' => new ServiceResource($service)
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $service = Service::find($id);
        if (!$service) {
            return response()->json([
                'status' => false,
                'message' => 'Service not found',
            ], 404);
        }

        return response()->json([
            'status' => true,
            'message' => 'Service retrieved successfully',
            'data' => new ServiceResource($service)
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $validateData = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'image' => 'required|file|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $service = Service::find($id);
        
        if (!$service) {
            return response()->json([
                'status' => false,
                'message' => 'Service not found',
            ], 404);
        }
        if ($validateData['image']) {
            $image = $request->file('image');
            $image_name = time() . '.' . $image->getClientOriginalExtension();
            $image->move(storage_path('app/public/images/services'), $image_name);
            $validateData['thumbnail'] = $image_name;
        }

        $service->update($validateData);

        return response()->json([
            'status' => true,
            'message' => 'Service updated successfully',
            'data' => new ServiceResource($service)
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $service = Service::find($id);
        if (!$service) {
            return response()->json([
                'status' => false,
                'message' => 'Service not found',
            ], 404);
        }
        $service->delete();
        return response()->json([
            'status' => true,
            'message' => 'Service deleted successfully',
        ], 200);
    }
}
