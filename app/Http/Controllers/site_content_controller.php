<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\SiteContent;

use App\Http\Resources\sectionContentResource;

class site_content_controller extends Controller
{
    public function index(){
        $content =  SiteContent::with('children') 
        ->whereNull('parent_id')
        ->orderBy('parent_id')
        ->get();
        return response()->json([
            'status' =>200 ,
            'message' =>'site Content fetched successfuly' ,
            'data' => sectionContentResource::collection($content) 
        ]);
    }
}
