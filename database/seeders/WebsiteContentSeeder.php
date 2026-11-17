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
            // Home Page Become a seller section
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
            // ['page' => 'home', 'field' => 'Become a Seller Background Color', 'value' => '#f8dfad2e', 'type' => 'color'],
            // Home page Why chalo bazaar section
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
            // About Banner Section
            ['page' => 'about-us', 'field' => 'Banner Title', 'value' => 'About Bazaar Chalo', 'type' => 'text'],
            ['page' => 'about-us', 'field' => 'Banner Background Image', 'value' => 'no_image', 'type' => 'image'],
            // About Main Section
            ['page' => 'about-us', 'field' => 'About Section Left Image', 'value' => 'no_image', 'type' => 'image'],
            ['page' => 'about-us', 'field' => 'About Section Subtitle', 'value' => 'Fresh shopping, local impact', 'type' => 'text'],
            ['page' => 'about-us', 'field' => 'About Section Title', 'value' => 'Your local bazaar, brought closer', 'type' => 'text'],
            ['page' => 'about-us', 'field' => 'About Section Descripion', 'value' => 'Your local bazaar, brought closer', 'type' => 'editor'],
            // About Us Guide Section
            ['page' => 'about-us', 'field' => 'Guide Section Subtitle', 'value' => 'What guides us', 'type' => 'text'],
            ['page' => 'about-us', 'field' => 'Guide Section Title', 'value' => 'Built around the neighbourhood', 'type' => 'text'],
            ['page' => 'about-us', 'field' => 'Guide Section Description', 'value' => 'We believe good food, good service, and strong communities belong together.', 'type' => 'textarea'],
            ['page' => 'about-us', 'field' => 'Guide Section Step1 icon', 'value' => 'fas fa-leaf', 'type' => 'text'],
            ['page' => 'about-us', 'field' => 'Guide Section Step1 Title', 'value' => 'Fresh and dependable', 'type' => 'text'],
            ['page' => 'about-us', 'field' => 'Guide Section Step1 Description', 'value' => 'We help you find quality products selected by sellers who know their community.', 'type' => 'text'],
            ['page' => 'about-us', 'field' => 'Guide Section Step2 icon', 'value' => 'fas fa-store', 'type' => 'text'],
            ['page' => 'about-us', 'field' => 'Guide Section Step2 Title', 'value' => 'Local sellers first', 'type' => 'text'],
            ['page' => 'about-us', 'field' => 'Guide Section Step2 Description', 'value' => 'Our marketplace gives neighbourhood shops a simple way to grow online.', 'type' => 'text'],
            ['page' => 'about-us', 'field' => 'Guide Section Step3 icon', 'value' => 'fas fa-heart', 'type' => 'text'],
            ['page' => 'about-us', 'field' => 'Guide Section Step3 Title', 'value' => 'Shopping with purpose', 'type' => 'text'],
            ['page' => 'about-us', 'field' => 'Guide Section Step3 Description', 'value' => 'Every order keeps more value close to home and supports the people around you.', 'type' => 'text'],

            // About How bazaar chalo work section
            ['page' => 'about-us', 'field' => 'How Bazaar Chalo Subtitle', 'value' => 'Simple by design', 'type' => 'text'],
            ['page' => 'about-us', 'field' => 'How Bazaar Chalo Title', 'value' => 'How Bazaar Chalo works', 'type' => 'text'],
            ['page' => 'about-us', 'field' => 'How Bazaar Chalo Description', 'value' => 'A better local shopping journey in three easy steps.', 'type' => 'textarea'],
            ['page' => 'about-us', 'field' => 'How Bazaar Chalo step1 Icon', 'value' => 'fas fa-search-location', 'type' => 'text'],
            ['page' => 'about-us', 'field' => 'How Bazaar Chalo step1 Title', 'value' => 'Find a nearby shop', 'type' => 'text'],
            ['page' => 'about-us', 'field' => 'How Bazaar Chalo step1 Description', 'value' => 'Discover trusted sellers and fresh products close to you.', 'type' => 'text'],
            ['page' => 'about-us', 'field' => 'How Bazaar Chalo step2 Icon', 'value' => 'fas fa-shopping-basket', 'type' => 'text'],
            ['page' => 'about-us', 'field' => 'How Bazaar Chalo step2 Title', 'value' => 'Choose what you need', 'type' => 'text'],
            ['page' => 'about-us', 'field' => 'How Bazaar Chalo step2 Description', 'value' => 'Build your basket with quality products from local stores.', 'type' => 'text'],
            ['page' => 'about-us', 'field' => 'How Bazaar Chalo step3 Icon', 'value' => 'fas fa-truck', 'type' => 'text'],
            ['page' => 'about-us', 'field' => 'How Bazaar Chalo step3 Title', 'value' => 'Enjoy easy delivery', 'type' => 'text'],
            ['page' => 'about-us', 'field' => 'How Bazaar Chalo step3 Description', 'value' => 'Get your order delivered with care, right to your doorstep.', 'type' => 'text'],

            // Contact page content in setting
            ['page' => 'setting', 'field' => 'Contact Banner Title', 'value' => 'Contact', 'type' => 'text'],
            ['page' => 'setting', 'field' => 'Contact Banner Background Image', 'value' => 'no_image', 'type' => 'image'],

            // Contact main heading
            ['page' => 'setting', 'field' => 'Contact Title', 'value' => 'Get in touch', 'type' => 'text'],

            // FAQ page content
            ['page' => 'faq', 'field' => 'FAQ Banner Title', 'value' => 'Frequently Asked Questions', 'type' => 'text'],
            ['page' => 'faq', 'field' => 'FAQ Banner Background Image', 'value' => 'no_image', 'type' => 'image'],
            ['page' => 'faq', 'field' => 'FAQ Subtitle', 'value' => 'Need a little help?', 'type' => 'text'],
            ['page' => 'faq', 'field' => 'FAQ Title', 'value' => 'How can we help?', 'type' => 'text'],
            ['page' => 'faq', 'field' => 'FAQ Description', 'value' => 'Find quick answers about shopping fresh, supporting local sellers, and receiving your order.', 'type' => 'textarea'],

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
