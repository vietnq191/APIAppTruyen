<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StoryCategories extends Model
{
    use HasFactory;

    protected $fillable = [
        'story_id',
        'category_id',
    ];

    public function getCategory()
    {
        return $this->belongsTo(Categories::class, 'category_id', 'id');
    }
}
