<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BoardOfDirctor extends Model
{
    protected $fillable = [
        'name',
        'position',
        'image_path',
        'description',
        'email',
        'phone'
    ];
}
