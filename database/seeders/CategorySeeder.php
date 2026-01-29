<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Artificial Intelligence (AI)', 'slug' => 'ai', 'is_active' => true],
            ['name' => 'Development', 'slug' => 'development', 'is_active' => true],
            ['name' => 'Marketing', 'slug' => 'marketing', 'is_active' => true],
            ['name' => 'Design', 'slug' => 'design', 'is_active' => true],
            ['name' => 'Photography & Video', 'slug' => 'photography', 'is_active' => true],
        ];

        foreach ($categories as $category) {
            Category::firstOrCreate(
                ['slug' => $category['slug']],
                $category
            );
        }
    }
}
