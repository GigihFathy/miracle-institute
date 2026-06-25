<?php

namespace Database\Seeders\Learning;

use App\Models\Material;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Database\Seeder;

class MaterialSeeder extends Seeder
{
    public function run(): void
    {
        $mentor = User::where('email', 'disciple@example.test')->firstOrFail();

        $templates = [
            ['type' => 'video', 'name_suffix' => 'Teaching Video', 'external_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ'],
            ['type' => 'pdf', 'name_suffix' => 'Lesson Notes', 'external_url' => 'https://example.test/files/lesson-notes.pdf'],
            ['type' => 'ppt', 'name_suffix' => 'Presentation Slides', 'external_url' => 'https://example.test/files/presentation-slides.pptx'],
        ];

        foreach (Topic::all() as $topic) {
            foreach ($templates as $index => $template) {
                Material::factory()->create([
                    'topic_id' => $topic->id,
                    'uploader_id' => $mentor->id,
                    'name' => $topic->name . ' - ' . $template['name_suffix'],
                    'type' => $template['type'],
                    'path' => null,
                    'external_url' => $template['external_url'],
                    'sort_order' => $index + 1,
                    'status' => 'active',
                ]);
            }
        }
    }
}