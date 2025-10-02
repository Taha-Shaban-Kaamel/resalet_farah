<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SiteContent;

class sitContentController extends Controller
{
    public function index()
    {
        $siteContents = SiteContent::all();
        return response()->json([
            'status' =>200 ,
            'message' =>'site Content fetched successfuly' ,
            'data' => $siteContents
        ]);
    }

    public function store(Request $request)
    {

       $validateData = $request->validate([
            'title' => 'required|string',
            'description' => 'required|string',
            'image' => 'required|file|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'order' => 'required|integer',
            'status' => 'required|boolean',
        ]);

        if($request->hasFile('image')){
            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->move(storage_path('app/public/images/site-content'), $imageName);
            $validateData['image'] = 'images/site-content/' . $imageName;
        }
        
        $siteContent = SiteContent::create([
            'title' => $validateData['title'],
            'description' => $validateData['description'],
            'image_url' => $validateData['image'],
            'order' => $validateData['order'],
            'status' => $validateData['status'],
        ]);
        return response()->json([
            'status' =>200 ,
            'message' =>'site Content created successfuly' ,
            'data' => $siteContent
        ]);
    }

    public function update(Request $request, $id)
    {
        $siteContent = SiteContent::findOrFail($id);
        $siteContent->update($request->all());
        return response()->json([
            'status' =>200 ,
            'message' =>'site Content updated successfuly' ,
            'data' => $siteContent
        ]);
    }

    public function destroy($id)
    {
        $siteContent = SiteContent::findOrFail($id);
        $siteContent->delete();
        return response()->json([
            'status' =>200 ,
            'message' =>'site Content deleted successfuly' ,
            'data' => $siteContent
        ]);
    }
}
