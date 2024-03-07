<?php

namespace Database\Seeders;

use App\Domains\Sector\Models\Sector;
use Database\Seeders\Traits\DisableForeignKeys;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SectorSeeder extends Seeder
{
    use DisableForeignKeys;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->disableForeignKeys();
        DB::table('sectors')->truncate();
        $sectors = [
            [
                'id' => 1,
                'name' => 'Automotive',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => 2,
                'name' => 'Chemicals and Speciality Materials',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => 3,
                'name' => 'Consumer Products',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => 4,
                'name' => 'Industrial Products and Services',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => 5,
                'name' => 'Retail, Wholesale and Distribution',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => 6,
                'name' => 'Travel, Hospitality and Services',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => 7,
                'name' => 'Mining',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => 8,
                'name' => 'Oil & Gas',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => 9,
                'name' => 'Power',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => 10,
                'name' => 'Shipping & Ports',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => 11,
                'name' => 'Water',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => 12,
                'name' => 'Banking & Securities',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => 13,
                'name' => 'Insurance',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => 14,
                'name' => 'Investment Management',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => 15,
                'name' => 'Real Estate',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => 16,
                'name' => 'Health Care',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => 17,
                'name' => 'Life Sciences',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => 18,
                'name' => 'Civil Government',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => 19,
                'name' => 'Defense',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => 20,
                'name' => 'Education',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => 21,
                'name' => 'International Donor Organizations',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => 22,
                'name' => 'Public Health and Social Services',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => 23,
                'name' => 'Public Transportation',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => 24,
                'name' => 'Security and Justice',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => 25,
                'name' => 'Technology',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => 26,
                'name' => 'Media',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => 27,
                'name' => 'Telecommunications',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => 28,
                'name' => 'Finance',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => 29,
                'name' => 'Information Technology',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => 30,
                'name' => 'Legal & Core Business',
                'created_at' => now(),
                'updated_at' => now()
            ],
        ];

        DB::table('sectors')->insert($sectors);
        $this->enableForeignKeys();
    }
}
