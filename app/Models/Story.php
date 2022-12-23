<?php

namespace App\Models;

use App\Traits\Filterable;
use App\Traits\Paginatable;
use App\Traits\Sortable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Story extends Model
{
    use HasFactory;
    use Paginatable, Filterable, Sortable;

    protected $fillable = [
        'title',
        'description',
        'image',
        'author_id',
        'source',
        'status',
    ];

    protected $filterable = [
        'keyword',
    ];

    public $sortables = ['created_at'];

    public function filterKeyword($query, $value)
    {
        return $query->where('title', 'LIKE', '%' . $value . '%');
    }

    public function getAuthor()
    {
        return $this->belongsTo(Author::class, 'author_id', 'id')->whereNull('authors.deleted_at');
    }

    public function getCategories()
    {
        return $this->hasMany(StoryCategories::class, 'story_id', 'id');
    }

    public function getChapters()
    {
        return $this->hasMany(Chapter::class, 'story_id', 'id');
    }
}
