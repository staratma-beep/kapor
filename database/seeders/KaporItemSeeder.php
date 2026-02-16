<?php

namespace Database\Seeders;

use App\Models\KaporItem;
use App\Models\KaporSize;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class KaporItemSeeder extends Seeder
{
    public function run(): void
    {
        // Clear existing data
        Schema::disableForeignKeyConstraints();
        KaporSize::truncate();
        KaporItem::truncate();
        Schema::enableForeignKeyConstraints();

        $items = [
            // 1. KEMEJA (Gender Specific Sizes) -> Tutup_Badan
            [
                'category' => 'Tutup_Badan',
                'item_name' => 'Kemeja',
                'sizes_config' => [
                    'L' => ['14', '14.5', '15', '15.5', '16', '16.5', '17', '17.5', '18', '18.5', '19', '19.5', '20', '21', '22'],
                    'P' => ['K', 'SD', 'B', 'EB', 'EEB', 'EEEB', 'EEEEB']
                ]
            ],
            // 2. CELANA/ROK (Gender Specific Sizes) -> Tutup_Badan
            [
                'category' => 'Tutup_Badan',
                'item_name' => 'Celana/Rok',
                'sizes_config' => [
                    'L' => range(27, 50),
                    'P' => ['K', 'SD', 'B', 'EB', 'EEB', 'EEEB', 'EEEEB']
                ]
            ],
            // 3. T-SHIRT/OLAHRAGA (Universal) -> Tutup_Badan
            [
                'category' => 'Tutup_Badan',
                'item_name' => 'T-Shirt/Olahraga',
                'sizes_config' => [
                    'U' => ['K', 'SD', 'B', 'EB', 'EEB', 'EEEB', 'EEEEB']
                ]
            ],
            // 4. JAKET (Universal) -> Tutup_Badan
            [
                'category' => 'Tutup_Badan',
                'item_name' => 'Jaket',
                'sizes_config' => [
                    'U' => ['K', 'SD', 'B', 'EB', 'EEB', 'EEEB', 'EEEEB']
                ]
            ],
            // 5. SEPATU DINAS (Universal numeric 36-48) -> Tutup_Kaki
            [
                'category' => 'Tutup_Kaki',
                'item_name' => 'Sepatu Dinas',
                'sizes_config' => [
                    'U' => range(36, 48)
                ]
            ],
            // 6. SEPATU OLAHRAGA (Universal numeric 36-48) -> Tutup_Kaki
            [
                'category' => 'Tutup_Kaki',
                'item_name' => 'Sepatu Olahraga',
                'sizes_config' => [
                    'U' => range(36, 48)
                ]
            ],
            // 7. SABUK (Universal numeric 36-60 even) -> Atribut
            [
                'category' => 'Atribut',
                'item_name' => 'Sabuk',
                'sizes_config' => [
                    'U' => range(36, 60, 2)
                ]
            ],
            // 8. JILBAB (Women Only) -> Tutup_Kepala
            [
                'category' => 'Tutup_Kepala',
                'item_name' => 'Jilbab',
                'gender_specific' => 'P',
                'sizes_config' => [
                    'P' => ['K', 'SD', 'B']
                ]
            ],
        ];

        foreach ($items as $itemData) {
            $sizesConfig = $itemData['sizes_config'];
            unset($itemData['sizes_config']);

            // Create Item
            $item = KaporItem::create($itemData);

            // Create Sizes
            foreach ($sizesConfig as $genderKey => $sizeList) {
                // Map 'U' to null for database
                $gender = ($genderKey === 'U') ? null : $genderKey;

                $order = 1;
                foreach ($sizeList as $sizeLabel) {
                    KaporSize::create([
                        'kapor_item_id' => $item->id,
                        'size_label' => (string)$sizeLabel,
                        'gender' => $gender,
                        'sort_order' => $order++,
                    ]);
                }
            }
        }
    }
}
