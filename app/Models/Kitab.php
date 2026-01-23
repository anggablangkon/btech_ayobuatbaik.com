<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kitab extends Model
{
    use HasFactory;

    protected $table = 'kitabs';

    protected $fillable = [
        'name',
        'author',
        'description',
        'slug',
        'cover_image',
        'is_active',
        'urutan',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get all chapters in this kitab
     */
    public function chapters()
    {
        return $this->hasMany(KitabChapter::class, 'kitab_id')->orderBy('urutan');
    }

    /**
     * Count total maqolah in this kitab
     */
    public function getTotalMaqolahAttribute()
    {
        return $this->chapters->sum(function ($chapter) {
            return $chapter->maqolahs->count();
        });
    }

    /**
     * Scope for active kitab only
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
