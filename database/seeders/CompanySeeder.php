<?php

namespace Database\Seeders;

use App\Domains\Company\Models\Company;
use Database\Seeders\Traits\DisableForeignKeys;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CompanySeeder extends Seeder
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
        DB::table('companies')->truncate();

        if (app()->environment(['local', 'testing'])) {
            Company::factory()->count(config('seeder.company_count_local'))->create();
        }

        $this->enableForeignKeys();
    }
}
