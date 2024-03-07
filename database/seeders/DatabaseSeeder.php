<?php

namespace Database\Seeders;

use Database\Seeders\Traits\TruncateTable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

/**
 * Class DatabaseSeeder.
 */
class DatabaseSeeder extends Seeder
{
    use TruncateTable;

    /**
     * Seed the application's database.
     */
    public function run()
    {
        Model::unguard();
        Schema::disableForeignKeyConstraints();
        $this->truncateMultiple([
            'activity_log',
            'failed_jobs'
        ]);

        $this->call(CountrySeeder::class);
        $this->call(SectorSeeder::class);
        $this->call(TimezoneSeeder::class);
        $this->call(CompanySeeder::class);
        $this->call(ExperienceSeeder::class);
        $this->call(CategorySeeder::class);
        $this->call(AuthSeeder::class);
        $this->call(JobSeeder::class);
        $this->call(AnnouncementSeeder::class);
        $this->call(JobApplicationSeeder::class);
        $this->call(NotificationSeeder::class);
        $this->call(EmojiSeeder::class);
        Schema::enableForeignKeyConstraints();
        Model::reguard();
    }
}
