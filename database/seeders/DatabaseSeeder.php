<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Project;
use App\Models\Setting;
use App\Models\User;
use App\Support\Catalog;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use RuntimeException;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the first admin account, the current site content and the confirmed contact details.
     * Safe to re-run: existing records are matched and left as they are.
     */
    public function run(): void
    {
        $email = env('ADMIN_EMAIL');
        $password = env('ADMIN_PASSWORD');
        if (! $email || ! $password) {
            throw new RuntimeException('Set ADMIN_EMAIL and ADMIN_PASSWORD in .env before seeding.');
        }

        $admin = User::query()->firstOrNew(['email' => $email]);
        if (! $admin->exists) {
            $admin->forceFill(['name' => env('ADMIN_NAME', 'Admin'), 'password' => $password, 'is_admin' => true])->save();
        }

        foreach (Catalog::sampleProducts() as $i => $product) {
            Product::query()->firstOrCreate(['slug' => $product['slug']], [
                'name' => $product['name'],
                'category' => $product['category'],
                'price' => $product['price'],
                'badge' => $product['badge'],
                'material' => $product['material'],
                'dimensions' => $product['dimensions'],
                'weight' => $product['weight'],
                'images' => $product['images'],
                'sort_order' => $i,
            ]);
        }

        foreach (Catalog::sampleProjects() as $i => $project) {
            Project::query()->firstOrCreate(['title' => $project['title']], [
                'category' => $project['category'],
                'summary' => $project['text'],
                'image' => $project['image'],
                'sort_order' => $i,
            ]);
        }

        foreach (Setting::FIELDS as $key => $field) {
            Setting::query()->firstOrCreate(['key' => $key], ['value' => config("artform.{$key}")]);
        }
    }
}
