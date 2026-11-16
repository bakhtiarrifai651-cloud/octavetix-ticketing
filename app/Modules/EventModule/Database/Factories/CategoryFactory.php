<?php

namespace App\Modules\EventModule\Database\Factories;

use App\Modules\EventModule\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CategoryFactory extends Factory
{
    protected $model = Category::class;

    public function definition(): array
    {
        $name = fake()->unique()->randomElement([
            'Seminar Nasional',
            'Workshop Coding',
            'Konser Musik',
            'Kompetisi IT',
            'Webinar Karir',
        ]);

        return [
            'name' => $name,
            'slug' => Str::slug($name),
        ];
    }
}