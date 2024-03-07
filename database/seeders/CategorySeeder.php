<?php

namespace Database\Seeders;

use App\Domains\Category\Models\Category;
use Database\Seeders\Traits\DisableForeignKeys;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
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
        DB::table('categories')->truncate();
        $categories = [
            [
                'id' => 1,
                'name' => 'UiPath',
                'class' => 'ui-path-type',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'name' => 'Blue Prism',
                'class' => 'blue-prism-type',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 3,
                'name' => 'Automation Anywhere',
                'class' => 'automation-anywhere-type',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 4,
                'name' => 'Power Automate',
                'class' => 'power-automate-type',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 5,
                'name' => 'Robocorp',
                'class' => 'ui-path-type',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 6,
                'name' => 'Others',
                'class' => 'customer-type',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];
        DB::table('categories')->insert($categories);
        $this->enableForeignKeys();
    }
}
