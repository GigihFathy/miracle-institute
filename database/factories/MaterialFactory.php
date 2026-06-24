<?php

namespace Database\Factories;

use App\Models\Material;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class MaterialFactory extends Factory
{
    protected $model = Material::class;

    public function definition(): array
    {
        return [
            'id' => (string) Str::uuid(),
            'topic_id' => Topic::factory(),
            'uploader_id' => User::factory(),
            'name' => $this->faker->words(3, true),
            'type' => 'pdf',
            'path' => null,
            'external_url' => null,
            'sort_order' => 1,
            'status' => 'active',
        ];
    }
}