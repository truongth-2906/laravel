<?php

namespace Database\Seeders;

use App\Domains\Auth\Models\User;
use App\Domains\Job\Models\Job;
use Database\Seeders\Traits\DisableForeignKeys;
use Database\Seeders\Traits\TruncateTable;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class JobApplicationSeeder extends Seeder
{
    use DisableForeignKeys, TruncateTable;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->disableForeignKeys();
        DB::table('job_applications')->delete();

        $freelancers = User::freelancers()->limit(10)->get();
        $jobs = Job::hasOpen()->limit(100)->pluck('id');

        foreach ($freelancers as $freelancer) {
            $freelancer->jobApplications()->syncWithPivotValues($jobs, ['status' => rand(1, 3)]);
        }
        $this->enableForeignKeys();
    }
}
