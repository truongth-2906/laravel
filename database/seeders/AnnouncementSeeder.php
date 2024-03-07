<?php

namespace Database\Seeders;

use App\Domains\Announcement\Models\Announcement;
use Database\Seeders\Traits\DisableForeignKeys;
use Database\Seeders\Traits\TruncateTable;
use Illuminate\Database\Seeder;

/**
 * Class AnnouncementSeeder.
 */
class AnnouncementSeeder extends Seeder
{
    use DisableForeignKeys, TruncateTable;

    /**
     * Run the database seeds.
     */
    public function run()
    {
        $this->disableForeignKeys();

        $this->truncate('announcements');

        $this->enableForeignKeys();
    }
}
