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
            ['page' => 'home', 'field' => 'Feature Category Subtitle', 'value' => 'Shop by collection', 'type' => 'text'],
            ['page' => 'home', 'field' => 'Feature Category Title', 'value' => 'Popular Categories', 'type' => 'text'],
            ['page' => 'home', 'field' => 'Feature Shops Subtitle', 'value' => 'Fresh from your neighbourhood', 'type' => 'text'],
            ['page' => 'home', 'field' => 'Feature Shops Title', 'value' => 'Popular Shops Near You', 'type' => 'text'],
            ['page' => 'home', 'field' => 'Feature Collections Subtitle', 'value' => 'Handpicked collections', 'type' => 'text'],
            ['page' => 'home', 'field' => 'Feature Collections Title', 'value' => 'Featured Collections', 'type' => 'text'],
            ['page' => 'home', 'field' => 'Feature Products Subtitle', 'value' => 'Handpicked for you', 'type' => 'text'],
            ['page' => 'home', 'field' => 'Feature Products Title', 'value' => 'Popular Products', 'type' => 'text'],

            ['page' => 'home', 'field' => 'Become a Seller Subtitle', 'value' => 'Grow with your community', 'type' => 'text'],
            ['page' => 'home', 'field' => 'Become a Seller Title', 'value' => 'Become a Seller on Bazaar Chalo', 'type' => 'text'],
            ['page' => 'home', 'field' => 'Become a Seller Description', 'value' => 'Take your shop online, reach more local customers, and manage your products and orders in one simple place.', 'type' => 'textarea'],
            ['page' => 'home', 'field' => 'Become a Seller step1 icon', 'value' => 'fas fa-store', 'type' => 'text'],
            ['page' => 'home', 'field' => 'Become a Seller step1 Title', 'value' => 'List your shop', 'type' => 'text'],
            ['page' => 'home', 'field' => 'Become a Seller step1 Description', 'value' => 'Set up your storefront', 'type' => 'text'],
            ['page' => 'home', 'field' => 'Become a Seller step2 icon', 'value' => 'fas fa-box-open', 'type' => 'text'],
            ['page' => 'home', 'field' => 'Become a Seller step2 Title', 'value' => 'Add products', 'type' => 'text'],
            ['page' => 'home', 'field' => 'Become a Seller step2 Description', 'value' => 'Showcase your stock', 'type' => 'text'],
            ['page' => 'home', 'field' => 'Become a Seller step3 icon', 'value' => 'fas fa-chart-line', 'type' => 'text'],
            ['page' => 'home', 'field' => 'Become a Seller step3 Title', 'value' => 'Grow sales', 'type' => 'text'],
            ['page' => 'home', 'field' => 'Become a Seller step3 Description', 'value' => 'Reach local buyers', 'type' => 'text'],
            ['page' => 'home', 'field' => 'Become a Seller Right Image', 'value' => 'no_image', 'type' => 'image'],
            ['page' => 'home', 'field' => 'Become a Seller Background Color', 'value' => '#f8dfad2e', 'type' => 'color'],

            ['page' => 'home', 'field' => 'Why Bazaar Chalo Subtitle', 'value' => 'Why choose us', 'type' => 'text'],
            ['page' => 'home', 'field' => 'Why Bazaar Chalo Title', 'value' => 'Why Bazaar Chalo', 'type' => 'text'],
            ['page' => 'home', 'field' => 'Why Bazaar Chalo Description', 'value' => 'A better way to shop local and help neighbourhood sellers thrive.', 'type' => 'textarea'],
            ['page' => 'home', 'field' => 'Why Bazaar Chalo step1 Icon', 'value' => 'fas fa-leaf', 'type' => 'text'],
            ['page' => 'home', 'field' => 'Why Bazaar Chalo step1 Title', 'value' => 'Fresh and local', 'type' => 'text'],
            ['page' => 'home', 'field' => 'Why Bazaar Chalo step1 Description', 'value' => 'Shop produce sourced from sellers close to you.', 'type' => 'text'],
            ['page' => 'home', 'field' => 'Why Bazaar Chalo step2 Icon', 'value' => 'fas fa-shield-alt', 'type' => 'text'],
            ['page' => 'home', 'field' => 'Why Bazaar Chalo step2 Title', 'value' => 'Trusted sellers', 'type' => 'text'],
            ['page' => 'home', 'field' => 'Why Bazaar Chalo step2 Description', 'value' => 'Buy with confidence from verified local shops.', 'type' => 'text'],
            ['page' => 'home', 'field' => 'Why Bazaar Chalo step3 Icon', 'value' => 'fas fa-hand-holding-heart', 'type' => 'text'],
            ['page' => 'home', 'field' => 'Why Bazaar Chalo step3 Title', 'value' => 'Support local', 'type' => 'text'],
            ['page' => 'home', 'field' => 'Why Bazaar Chalo step3 Description', 'value' => 'Every order helps a small business grow.', 'type' => 'text'],
            ['page' => 'home', 'field' => 'Why Bazaar Chalo step4 Icon', 'value' => 'fas fa-shipping-fast', 'type' => 'text'],
            ['page' => 'home', 'field' => 'Why Bazaar Chalo step4 Title', 'value' => 'Easy delivery', 'type' => 'text'],
            ['page' => 'home', 'field' => 'Why Bazaar Chalo step4 Description', 'value' => 'Get your everyday essentials delivered with ease.', 'type' => 'text'],
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
