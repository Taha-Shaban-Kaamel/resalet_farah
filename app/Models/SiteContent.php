<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SiteContent extends Model
{
    protected $fillable = ['section_key', 'title', 'content', 'image_path', 'parent_id'];

    /**
     * Get the parent content.
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(SiteContent::class, 'parent_id');
    }

    /**
     * Get the children contents.
     */
    public function children(): HasMany
    {
        return $this->hasMany(SiteContent::class, 'parent_id');
    }
}
