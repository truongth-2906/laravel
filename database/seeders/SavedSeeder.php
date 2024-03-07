<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Database\Seeders\Traits\DisableForeignKeys;
use Database\Seeders\Traits\TruncateTable;
use App\Domains\Auth\Models\User;
use App\Domains\Job\Models\Job;
use App\Domains\Saved\Models\Saved;
use Illuminate\Support\Facades\DB;

class SavedSeeder extends Seeder
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
        DB::table('saveds')->delete();

        $users = User::where('type', USER::TYPE_EMPLOYER)->orWhere('type', USER::TYPE_FREELANCER)->limit(10)->get();

        foreach ($users as $user) {
            $jobs = Job::where('user_id', '!=', $user->id)->limit(100)->get();
            foreach ($jobs as $job) {
                $saved = new Saved();
                $saved->user()->associate($user);
                $job->savers()->save($saved);
            }
        }

        $employers = User::where('type', USER::TYPE_EMPLOYER)->limit(10)->get();
        $freelancers = User::where('type', USER::TYPE_FREELANCER)->limit(100)->get();

        foreach ($employers as $employer) {
            foreach ($freelancers as $freelancer) {
                $saved = new Saved();
                $saved->user()->associate($employer);
                $freelancer->savers()->save($saved);
            }
        }

        $this->enableForeignKeys();
    }
}
