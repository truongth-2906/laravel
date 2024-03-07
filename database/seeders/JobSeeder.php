<?php

namespace Database\Seeders;

use App\Domains\Auth\Models\User;
use App\Domains\Category\Models\Category;
use App\Domains\Company\Models\Company;
use App\Domains\Country\Models\Country;
use App\Domains\Experience\Models\Experience;
use App\Domains\Job\Models\Job;
use App\Domains\Timezone\Models\Timezone;
use Database\Seeders\Traits\DisableForeignKeys;
use Database\Seeders\Traits\TruncateTable;
use Illuminate\Database\Seeder;

class JobSeeder extends Seeder
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
        $this->truncateMultiple([
            'jobs',
            'category_job',
            'saveds',
        ]);

        $countries = Country::pluck('id')->toArray();
        $experiences = Experience::pluck('id')->toArray();
        $companies = Company::pluck('id')->toArray();
        $timezones = Timezone::pluck('id')->toArray();
        $users = User::where('type', USER::TYPE_EMPLOYER)->pluck('id')->toArray();

        if (app()->environment(['local', 'testing'])) {
            Job::factory()->country($countries)->experience($experiences)->company($companies)
                ->timezone($timezones)->user($users)->status()->count(config('seeder.job_count_local'))->create();
        }

        $categories = array_flip(Category::pluck('id')->toArray());

        Job::all()->each(function ($job) use ($categories) {
            $job->categories()->attach(
                array_rand($categories, 3)
            );
        });
        $this->enableForeignKeys();
    }
}
