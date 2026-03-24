<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Main demo user (login credentials)
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@payrexlaravel.com',
            'password' => 'raw15UW^1V3TL8?Bv8qa',
            'country' => 'PH',
        ]);

        $customers = [
            ['name' => 'Juan dela Cruz', 'email' => 'juan@example.com', 'phone' => '+639171234567', 'address_line1' => '123 Rizal Street', 'address_line2' => 'Barangay San Antonio', 'city' => 'Makati', 'state' => 'Metro Manila', 'postal_code' => '1200', 'country' => 'PH'],
            ['name' => 'Maria Santos', 'email' => 'maria.santos@example.com', 'phone' => '+639181234567', 'address_line1' => '456 Bonifacio Avenue', 'city' => 'Taguig', 'state' => 'Metro Manila', 'postal_code' => '1630', 'country' => 'PH'],
            ['name' => 'Pedro Reyes', 'email' => 'pedro.reyes@example.com', 'phone' => '+639191234567', 'address_line1' => '789 Mabini Street', 'address_line2' => 'Unit 4B', 'city' => 'Quezon City', 'state' => 'Metro Manila', 'postal_code' => '1100', 'country' => 'PH'],
            ['name' => 'Ana Garcia', 'email' => 'ana.garcia@example.com', 'phone' => '+639201234567', 'address_line1' => '321 Osmeña Boulevard', 'city' => 'Cebu City', 'state' => 'Cebu', 'postal_code' => '6000', 'country' => 'PH'],
            ['name' => 'Carlos Mendoza', 'email' => 'carlos@example.com', 'phone' => '+639211234567', 'address_line1' => '555 Aguinaldo Highway', 'city' => 'Dasmariñas', 'state' => 'Cavite', 'postal_code' => '4114', 'country' => 'PH'],
            ['name' => 'Sofia Villanueva', 'email' => 'sofia.v@example.com', 'phone' => '+639221234567', 'address_line1' => '88 Session Road', 'city' => 'Baguio', 'state' => 'Benguet', 'postal_code' => '2600', 'country' => 'PH'],
            ['name' => 'Miguel Torres', 'email' => 'miguel.torres@example.com', 'phone' => '+639231234567', 'address_line1' => '42 Lacson Street', 'city' => 'Bacolod', 'state' => 'Negros Occidental', 'postal_code' => '6100', 'country' => 'PH'],
            ['name' => 'Isabella Cruz', 'email' => 'isabella.cruz@example.com', 'phone' => '+639241234567', 'address_line1' => '10 Magsaysay Avenue', 'address_line2' => 'Brgy. Lahug', 'city' => 'Davao City', 'state' => 'Davao del Sur', 'postal_code' => '8000', 'country' => 'PH'],
            ['name' => 'Rafael Aquino', 'email' => 'rafael.aquino@example.com', 'phone' => '+639251234567', 'address_line1' => '77 España Boulevard', 'city' => 'Manila', 'state' => 'Metro Manila', 'postal_code' => '1008', 'country' => 'PH'],
            ['name' => 'Gabriela Lim', 'email' => 'gabriela.lim@example.com', 'phone' => '+639261234567', 'address_line1' => '15 Roxas Boulevard', 'address_line2' => 'Suite 301', 'city' => 'Pasay', 'state' => 'Metro Manila', 'postal_code' => '1300', 'country' => 'PH'],
        ];

        foreach ($customers as $customer) {
            User::factory()->create($customer);
        }

        $products = [
            ['name' => 'Wireless Bluetooth Headphones', 'description' => 'Premium noise-canceling over-ear headphones.', 'price' => 250000, 'category' => 'product', 'image' => 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=200&h=200&fit=crop'],
            ['name' => 'Leather Tote Bag', 'description' => 'Handcrafted full-grain leather tote.', 'price' => 320000, 'category' => 'product', 'image' => 'https://images.unsplash.com/photo-1590874103328-eac38a683ce7?w=200&h=200&fit=crop'],
            ['name' => 'UI Component Kit', 'description' => 'Figma + code templates for 50+ components.', 'price' => 149900, 'category' => 'product', 'image' => 'https://images.unsplash.com/photo-1558655146-9f40138edfeb?w=200&h=200&fit=crop'],
            ['name' => 'Sticker Pack', 'description' => 'Set of 5 vinyl developer stickers.', 'price' => 15000, 'category' => 'product', 'image' => 'https://images.unsplash.com/photo-1572375992501-4b0892d50c69?w=200&h=200&fit=crop'],

            ['name' => '1-Hour Consultation', 'description' => 'One-on-one technical consultation session.', 'price' => 150000, 'category' => 'service', 'image' => 'https://images.unsplash.com/photo-1557804506-669a67965ba0?w=200&h=200&fit=crop'],
            ['name' => 'Logo Design Package', 'description' => '3 concepts, 2 revisions, all source files.', 'price' => 750000, 'category' => 'service', 'image' => 'https://images.unsplash.com/photo-1626785774573-4b799315345d?w=200&h=200&fit=crop'],
            ['name' => 'Pro Plan - Monthly', 'description' => 'Full access to all features, billed monthly.', 'price' => 99900, 'category' => 'service', 'image' => 'https://images.unsplash.com/photo-1460925895917-afdab827c52f?w=200&h=200&fit=crop'],
            ['name' => 'Pro Plan - Annual', 'description' => 'Full access, billed annually. Save 20%.', 'price' => 959900, 'category' => 'service', 'image' => 'https://images.unsplash.com/photo-1551288049-bebda4e38f71?w=200&h=200&fit=crop'],
            ['name' => 'Online Course - Laravel Mastery', 'description' => 'Self-paced video course with lifetime access.', 'price' => 299900, 'category' => 'service', 'image' => 'https://images.unsplash.com/photo-1501504905252-473c47e087f8?w=200&h=200&fit=crop'],
            ['name' => 'Enterprise License - Annual', 'description' => 'Unlimited seats, priority support, SLA.', 'price' => 5000000, 'category' => 'service', 'image' => 'https://images.unsplash.com/photo-1497366216548-37526070297c?w=200&h=200&fit=crop'],
            ['name' => 'Cloud Hosting - Monthly', 'description' => 'Managed server with daily backups.', 'price' => 250000, 'category' => 'service', 'image' => 'https://images.unsplash.com/photo-1544197150-b99a580bb7a8?w=200&h=200&fit=crop'],
            ['name' => 'Priority Support', 'description' => 'Dedicated support with 1-hour SLA.', 'price' => 500000, 'category' => 'service', 'image' => 'https://images.unsplash.com/photo-1486312338219-ce68d2c6f44d?w=200&h=200&fit=crop'],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}
