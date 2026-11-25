<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Akun Admin (Kamu)
        User::create([
            'name' => 'Admin Dropship',
            'email' => 'admin@sayur.test',
            'phone' => '081200000001',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'address' => 'Kantor Pusat',
        ]);

        // 2. Akun Petani (Temanmu)
        $farmer = User::create([
            'name' => 'Kebun Pak Tani',
            'email' => 'petani@sayur.test',
            'phone' => '081200000002',
            'password' => Hash::make('password123'),
            'role' => 'farmer',
            'address' => 'Area Perkebunan Sayur',
        ]);

        // 3. Akun Pembeli Uji Coba
        User::create([
            'name' => 'Ibu Sari',
            'email' => 'pembeli@sayur.test',
            'phone' => '081200000003',
            'password' => Hash::make('password123'),
            'role' => 'customer',
            'address' => 'Jl. Merdeka No. 10',
        ]);

        // 4. Katalog Sayuran Awal
        $products = [
            ['name' => 'Bayam Hijau Segar', 'unit' => '1 ikat', 'base_price' => 3000, 'selling_price' => 4500, 'daily_quota' => 30],
            ['name' => 'Wortel Organik', 'unit' => '500 gram', 'base_price' => 6000, 'selling_price' => 8500, 'daily_quota' => 25],
            ['name' => 'Tomat Merah', 'unit' => '500 gram', 'base_price' => 5000, 'selling_price' => 7500, 'daily_quota' => 20],
            ['name' => 'Brokoli Hijau', 'unit' => '500 gram', 'base_price' => 11000, 'selling_price' => 15000, 'daily_quota' => 15],
            ['name' => 'Cabai Rawit Merah', 'unit' => '250 gram', 'base_price' => 12000, 'selling_price' => 16000, 'daily_quota' => 20],
        ];

        foreach ($products as $item) {
            Product::create([
                'farmer_id' => $farmer->id,
                'name' => $item['name'],
                'slug' => Str::slug($item['name']),
                'unit' => $item['unit'],
                'base_price' => $item['base_price'],
                'selling_price' => $item['selling_price'],
                'daily_quota' => $item['daily_quota'],
                'is_available' => true,
            ]);
        }
    }
}