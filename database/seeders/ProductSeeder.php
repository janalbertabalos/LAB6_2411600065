<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Sample products for FitPulse Tracker – fitness equipment & supplements theme
     * with varied stock levels to demonstrate low-stock alerts.
     */
    public function run(): void
    {
        $products = [
            [
                'name' => 'Adjustable Dumbbell Set 5-52.5 lbs',
                'sku' => 'FT-DMB-ADJ',
                'description' => 'Space-saving adjustable dumbbells, 5 to 52.5 lbs per hand. Perfect for home gyms.',
                'category' => 'Strength',
                'quantity' => 18,
                'reorder_level' => 6,
                'unit_price' => 349.99,
                'supplier' => 'PowerFit Equipment',
            ],
            [
                'name' => 'Olympic Barbell 45 lb',
                'sku' => 'FT-BAR-45',
                'description' => 'Standard 7-foot Olympic barbell, 45 lb, chrome finish with knurled grip.',
                'category' => 'Strength',
                'quantity' => 12,
                'reorder_level' => 5,
                'unit_price' => 189.00,
                'supplier' => 'PowerFit Equipment',
            ],
            [
                'name' => 'Resistance Band Set (5 levels)',
                'sku' => 'FT-RBD-5PC',
                'description' => 'Complete resistance band set with 5 tension levels, handles, door anchor, and carry bag.',
                'category' => 'Accessories',
                'quantity' => 45,
                'reorder_level' => 15,
                'unit_price' => 29.99,
                'supplier' => 'FlexGear Co.',
            ],
            [
                'name' => 'Yoga Mat Premium 6mm',
                'sku' => 'FT-YGA-6MM',
                'description' => 'Extra-thick 6mm non-slip yoga mat with alignment marks. Eco-friendly TPE material.',
                'category' => 'Accessories',
                'quantity' => 60,
                'reorder_level' => 20,
                'unit_price' => 34.50,
                'supplier' => 'ZenMotion',
            ],
            [
                'name' => 'Foam Roller High-Density 18"',
                'sku' => 'FT-FRM-18',
                'description' => 'Firm high-density foam roller for muscle recovery and myofascial release. 18-inch length.',
                'category' => 'Recovery',
                'quantity' => 28,
                'reorder_level' => 10,
                'unit_price' => 24.99,
                'supplier' => 'RecoverPro',
            ],
            [
                'name' => 'Whey Protein Isolate 5 lb – Chocolate',
                'sku' => 'SP-WPI-5CH',
                'description' => 'Premium whey protein isolate, 25g protein per serving, low carb. Chocolate flavor, 5 lb tub.',
                'category' => 'Supplements',
                'quantity' => 35,
                'reorder_level' => 12,
                'unit_price' => 64.99,
                'supplier' => 'NutriPeak Labs',
            ],
            [
                'name' => 'Creatine Monohydrate 300g',
                'sku' => 'SP-CRT-300',
                'description' => 'Micronized creatine monohydrate powder. 5g per scoop, unflavored. Supports strength and power.',
                'category' => 'Supplements',
                'quantity' => 50,
                'reorder_level' => 15,
                'unit_price' => 19.99,
                'supplier' => 'NutriPeak Labs',
            ],
            [
                'name' => 'Pre-Workout Formula 30 Servings',
                'sku' => 'SP-PRE-30',
                'description' => 'High-stim pre-workout with caffeine, beta-alanine, and citrulline. Fruit Punch flavor.',
                'category' => 'Supplements',
                'quantity' => 8,
                'reorder_level' => 10,
                'unit_price' => 39.99,
                'supplier' => 'NutriPeak Labs',
            ],
            [
                'name' => 'Kettlebell Cast Iron 35 lb',
                'sku' => 'FT-KTB-35',
                'description' => 'Solid cast iron kettlebell with powder-coated finish. 35 lb for swings, presses, and squats.',
                'category' => 'Strength',
                'quantity' => 14,
                'reorder_level' => 6,
                'unit_price' => 54.00,
                'supplier' => 'PowerFit Equipment',
            ],
            [
                'name' => 'Jump Rope Speed Cable',
                'sku' => 'FT-JMP-SPD',
                'description' => 'Adjustable speed jump rope with ball bearings. Ideal for HIIT and cardio conditioning.',
                'category' => 'Cardio',
                'quantity' => 40,
                'reorder_level' => 12,
                'unit_price' => 14.99,
                'supplier' => 'FlexGear Co.',
            ],
            [
                'name' => 'Pull-Up Bar Doorway Mount',
                'sku' => 'FT-PUB-DR',
                'description' => 'No-screw doorway pull-up bar. Supports up to 300 lbs. Multiple grip positions.',
                'category' => 'Strength',
                'quantity' => 22,
                'reorder_level' => 8,
                'unit_price' => 29.99,
                'supplier' => 'HomeGym Pro',
            ],
            [
                'name' => 'Ab Wheel Roller with Knee Pad',
                'sku' => 'FT-ABW-RL',
                'description' => 'Dual-wheel ab roller with comfortable knee pad. Builds core strength and stability.',
                'category' => 'Accessories',
                'quantity' => 3,
                'reorder_level' => 8,
                'unit_price' => 18.50,
                'supplier' => 'FlexGear Co.',
            ],
            [
                'name' => 'Massage Gun Deep Tissue',
                'sku' => 'FT-MSG-DT',
                'description' => 'Percussion massage gun with 6 heads and 30 speed levels. Quiet brushless motor.',
                'category' => 'Recovery',
                'quantity' => 9,
                'reorder_level' => 5,
                'unit_price' => 119.99,
                'supplier' => 'RecoverPro',
            ],
            [
                'name' => 'Gym Gloves Wrist Support',
                'sku' => 'FT-GLV-WS',
                'description' => 'Padded lifting gloves with integrated wrist wraps. Full-finger protection for heavy lifts.',
                'category' => 'Accessories',
                'quantity' => 55,
                'reorder_level' => 18,
                'unit_price' => 22.99,
                'supplier' => 'FlexGear Co.',
            ],
            [
                'name' => 'Resistance Tube Set with Handles',
                'sku' => 'FT-TUB-SET',
                'description' => 'Stackable resistance tubes (light to extra-heavy) with handles, ankle straps, and door anchor.',
                'category' => 'Accessories',
                'quantity' => 0,
                'reorder_level' => 10,
                'unit_price' => 27.99,
                'supplier' => 'FlexGear Co.',
            ],
            [
                'name' => 'BCAA Powder 30 Servings – Blue Raspberry',
                'sku' => 'SP-BCA-30',
                'description' => '2:1:1 BCAA formula for muscle recovery and endurance. Blue Raspberry flavor, 30 servings.',
                'category' => 'Supplements',
                'quantity' => 16,
                'reorder_level' => 8,
                'unit_price' => 29.99,
                'supplier' => 'NutriPeak Labs',
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}