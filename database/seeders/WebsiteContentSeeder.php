<?php

namespace Database\Seeders;

use App\Models\WebContent;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class WebsiteContentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $contents = [
            ['page' => 'setting', 'field' => 'Website Favicon', 'value' => 'no_image', 'type' => 'image'],
            ['page' => 'setting', 'field' => 'Website Header Logo', 'value' => 'no_image', 'type' => 'image'],
            ['page' => 'setting', 'field' => 'Website Footer Logo', 'value' => 'no_image', 'type' => 'image'],
            // ['page' => 'setting', 'field' => 'Website Primary Color', 'value' => '#002b49', 'type' => 'color'],
            // ['page' => 'setting', 'field' => 'Website Secondary Color', 'value' => '#ffb524', 'type' => 'color'],
            ['page' => 'setting', 'field' => 'Website Facebook Url', 'value' => 'https://www.facebook.com/', 'type' => 'url'],
            ['page' => 'setting', 'field' => 'Website Instagram Url', 'value' => 'https://www.istagram.com/', 'type' => 'url'],
            ['page' => 'setting', 'field' => 'Website Tiktok Url', 'value' => 'https://www.tiktok.com/', 'type' => 'url'],
            ['page' => 'setting', 'field' => 'Website Youtube Url', 'value' => 'https://www.youtube.com/', 'type' => 'url'],
            ['page' => 'setting', 'field' => 'Website Email', 'value' => 'info@bazaarchalo.com', 'type' => 'email'],
            ['page' => 'setting', 'field' => 'Website Phone Number', 'value' => '03306490627', 'type' => 'text'],
            ['page' => 'setting', 'field' => 'Website Address', 'value' => 'Bank Road Daska', 'type' => 'textarea'],
            // ['page' => 'setting', 'field' => 'Website Description', 'value' => '<p>Maketing Place Website</p>', 'type' => 'editor'],
            ['page' => 'home', 'field' => 'Hero Section Subtitle', 'value' => 'Sell your local products', 'type' => 'text'],
            ['page' => 'home', 'field' => 'Hero Section Title', 'value' => 'Apne Bazaar Se Apne Ghar Tak', 'type' => 'text'],
            ['page' => 'home', 'field' => 'Hero Section Background Image', 'value' => 'no_image', 'type' => 'image'],
        ];

        foreach ($contents as $content) {
            WebContent::firstOrCreate(
                [
                    'page' => $content['page'],
                    'field' => $content['field'],
                ],
                [
                    'value' => $content['value'],
                    'type' => $content['type'],
                ]
            );
        }
    }
}
