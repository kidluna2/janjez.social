<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Service;
use App\Models\User;
use Illuminate\Database\Seeder;

class SmmPanelSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'admin@smmpanel.co.ke'],
            [
                'name' => 'Admin User',
                'password' => bcrypt('admin123'),
                'role' => 'admin',
                'balance' => 10000,
                'phone' => '0712345678',
                'country' => 'Kenya',
            ]
        );

        $categories = [
            ['name' => 'Instagram', 'slug' => 'instagram', 'icon' => '📸', 'description' => 'Instagram services - followers, likes, views', 'order' => 1],
            ['name' => 'TikTok', 'slug' => 'tiktok', 'icon' => '🎵', 'description' => 'TikTok services - followers, likes, views', 'order' => 2],
            ['name' => 'YouTube', 'slug' => 'youtube', 'icon' => '▶️', 'description' => 'YouTube services - views, subscribers, likes', 'order' => 3],
            ['name' => 'X (Twitter)', 'slug' => 'twitter', 'icon' => '🐦', 'description' => 'X (Twitter) services - followers, retweets, likes', 'order' => 4],
            ['name' => 'Facebook', 'slug' => 'facebook', 'icon' => '📘', 'description' => 'Facebook services - likes, followers, shares', 'order' => 5],
            ['name' => 'Telegram', 'slug' => 'telegram', 'icon' => '✈️', 'description' => 'Telegram services - members, views', 'order' => 6],
        ];

        foreach ($categories as $category) {
            Category::firstOrCreate(['slug' => $category['slug']], $category);
        }

        $services = [
            ['name' => 'Instagram Followers', 'category_slug' => 'instagram', 'price' => 150, 'min_quantity' => 100, 'max_quantity' => 100000],
            ['name' => 'Instagram Likes', 'category_slug' => 'instagram', 'price' => 50, 'min_quantity' => 50, 'max_quantity' => 50000],
            ['name' => 'Instagram Views', 'category_slug' => 'instagram', 'price' => 20, 'min_quantity' => 1000, 'max_quantity' => 1000000],
            ['name' => 'TikTok Followers', 'category_slug' => 'tiktok', 'price' => 200, 'min_quantity' => 100, 'max_quantity' => 50000],
            ['name' => 'TikTok Likes', 'category_slug' => 'tiktok', 'price' => 60, 'min_quantity' => 50, 'max_quantity' => 100000],
            ['name' => 'TikTok Views', 'category_slug' => 'tiktok', 'price' => 10, 'min_quantity' => 1000, 'max_quantity' => 5000000],
            ['name' => 'YouTube Views', 'category_slug' => 'youtube', 'price' => 30, 'min_quantity' => 1000, 'max_quantity' => 1000000],
            ['name' => 'YouTube Subscribers', 'category_slug' => 'youtube', 'price' => 500, 'min_quantity' => 100, 'max_quantity' => 10000],
            ['name' => 'X (Twitter) Followers', 'category_slug' => 'twitter', 'price' => 300, 'min_quantity' => 100, 'max_quantity' => 50000],
            ['name' => 'X (Twitter) Likes', 'category_slug' => 'twitter', 'price' => 80, 'min_quantity' => 50, 'max_quantity' => 50000],
            ['name' => 'Facebook Page Likes', 'category_slug' => 'facebook', 'price' => 100, 'min_quantity' => 100, 'max_quantity' => 50000],
            ['name' => 'Telegram Members', 'category_slug' => 'telegram', 'price' => 400, 'min_quantity' => 100, 'max_quantity' => 50000],
        ];

        foreach ($services as $service) {
            $category = Category::where('slug', $service['category_slug'])->first();
            if ($category) {
                Service::firstOrCreate(
                    ['name' => $service['name'], 'category_id' => $category->id],
                    [
                        'slug' => strtolower(str_replace(' ', '-', $service['name'])),
                        'price' => $service['price'],
                        'min_quantity' => $service['min_quantity'],
                        'max_quantity' => $service['max_quantity'],
                        'status' => 'active',
                    ]
                );
            }
        }
    }
}
