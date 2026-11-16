<?php

namespace App\Modules\EventModule\Models;

use App\Modules\EventModule\Database\Factories\EventFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'event_date',
        'location',
    ];

    protected function casts(): array
    {
        return [
            'event_date' => 'datetime',
        ];
    }

    // Relasi Many-to-Many dengan tabel pivot category_event
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'category_event')->withTimestamps();
    }

    protected static function newFactory()
    {
        return EventFactory::new();
    }
}