<?php

namespace Database\Seeders\Auth;

use App\Domains\Auth\Models\User;
use App\Domains\Category\Models\Category;
use App\Domains\Company\Models\Company;
use App\Domains\Country\Models\Country;
use App\Domains\Experience\Models\Experience;
use App\Domains\Sector\Models\Sector;
use App\Domains\Timezone\Models\Timezone;
use Database\Seeders\Traits\DisableForeignKeys;
use Illuminate\Database\Seeder;

/**
 * Class UserTableSeeder.
 */
class UserSeeder extends Seeder
{
    use DisableForeignKeys;

    /**
     * Run the database seed.
     */
    public function run()
    {
        $this->disableForeignKeys();

        // Add the master administrator, user id of 1
        User::create([
            'type' => User::TYPE_ADMIN,
            'name' => 'Super Admin',
            'firstname' => 'Super',
            'lastname' => 'Admin',
            'username' => 'admin',
            'email' => 'admin@raau01.dev',
            'password' => config('base.default_password'),
            'email_verified_at' => now(),
            'active' => true,
        ]);

        if (app()->environment(['local', 'testing'])) {
            User::create([
                'type' => User::TYPE_FREELANCER,
                'name' => 'Test Freelancer',
                'firstname' => 'Test',
                'lastname' => 'Freelancer',
                'username' => 'freelancer',
                'email' => 'freelancer@raau01.dev',
                'password' => config('base.default_password'),
                'email_verified_at' => now(),
                'active' => true,
            ]);
        }

        if (app()->environment(['local', 'testing'])) {
            User::create([
                'type' => User::TYPE_EMPLOYER,
                'name' => 'Test Employer',
                'firstname' => 'Test',
                'lastname' => 'Employer',
                'username' => 'employer',
                'email' => 'employer@raau01.dev',
                'password' => config('base.default_password'),
                'email_verified_at' => now(),
                'active' => true,
            ]);
        }

        $countries = Country::pluck('id')->toArray();
        $experiences = Experience::pluck('id')->toArray();
        $companies = Company::pluck('id')->toArray();
        $timezones = Timezone::pluck('id')->toArray();
        $sectors = Sector::pluck('id')->toArray();

        if (app()->environment(['local', 'testing'])) {
            User::factory()->employer()->country($countries)->experience($experiences)->company($companies)
                ->timezone($timezones)->sector($sectors)->count(config('seeder.employer_count_local'))->create();
            User::factory()->freelancer()->country($countries)->experience($experiences)->company($companies)
                ->timezone($timezones)->sector($sectors)->count(config('seeder.freelancer_count_local'))->create();
        }

        $categories = array_flip(Category::pluck('id')->toArray());

        User::all()->each(function ($user) use ($categories) {
            $user->categories()->attach(
                array_rand($categories, 3)
            );
        });

        $this->enableForeignKeys();
    }
}
