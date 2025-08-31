<?php

namespace App\Http\Controllers;

use App\Http\Resources\SliderResource;
use App\Models\Slider;
use Illuminate\Http\Request;

class slidersController extends Controller
{
    public function index()
    {
        $sliders = Slider::all();
        return response()->json([
            'status' => 200,
            'message' => 'slides',
            'data' => SliderResource::collection($sliders)
        ]);
    }

    public function create(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'description' => 'required',
            'image' => 'required|file|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'order' => 'required',
            'status' => 'nullable',
        ]);

        $image = $request->file('image');

        $image_name = time() . '.' . $image->getClientOriginalExtension();

        $image->move(storage_path('app/public/images/sliders'), $image_name);

        $slider = Slider::create([
            'title' => $request->title,
            'description' => $request->description,
            'image_url' => $image_name,
            'order' => $request->order,
            'status' => $request->status,
        ]);

        return response()->json([
            'status' => 200,
            'message' => 'slide created',
            'data' => new SliderResource($slider)
        ]);
    }
}
