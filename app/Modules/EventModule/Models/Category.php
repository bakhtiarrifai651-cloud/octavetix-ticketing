<?php

namespace App\Modules\EventModule\Models;

use App\Modules\EventModule\Database\Factories\CategoryFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
    ];

    // Relasi Many-to-Many dengan tabel pivot category_event
    public function events(): BelongsToMany
    {
        return $this->belongsToMany(Event::class, 'category_event')->withTimestamps();
    }

    protected static function newFactory()
    {
        return CategoryFactory::new();
    }
}