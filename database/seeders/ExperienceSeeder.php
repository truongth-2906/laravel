<?php

namespace Database\Seeders;

use App\Domains\Experience\Models\Experience;
use Database\Seeders\Traits\DisableForeignKeys;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ExperienceSeeder extends Seeder
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
        DB::table('experiences')->truncate();
        $experiences = [
            ['name' => '0 to 1 years'],
            ['name' => '1 to 3 years'],
            ['name' => '3 to 5 years'],
            ['name' => '5 + years'],
        ];
        foreach ($experiences as $value) {
            Experience::create($value);
        }
        $this->enableForeignKeys();
    }
}
