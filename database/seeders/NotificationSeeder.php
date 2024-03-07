<?php

namespace Database\Seeders;

use App\Domains\Auth\Models\User;
use App\Domains\Job\Models\Job;
use App\Domains\JobApplication\Models\JobApplication;
use App\Domains\Notification\Models\Notification;
use Database\Seeders\Traits\DisableForeignKeys;
use Database\Seeders\Traits\TruncateTable;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NotificationSeeder extends Seeder
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
        DB::table('notification_user')->delete();
        DB::table('notifications')->delete();

        $jobs = Job::where('status', Job::STATUS_OPEN)->has('applicants')->with('applicants', 'user')->limit(100)->get();

        foreach ($jobs as $job) {
            foreach ($job->applicants as $applicant) {
                if ($applicant->application->status == JobApplication::STATUS_PENDING) {
                    $notification = Notification::create([
                        'type' => $this->convertToNotificationType($applicant->application->status),
                        'sender_id' => $applicant->id,
                        'notifiable_id' => $job->id,
                        'notifiable_type' => Notification::JOB_NOTIFIABLE_TYPE,
                    ]);
                    $notification->receivers()->attach($job->user->id, ['is_read' => false]);
                } elseif ($applicant->application->status == JobApplication::STATUS_DONE) {
                    $notification = Notification::firstOrCreate(
                        [
                            'sender_id' => $job->user->id,
                            'notifiable_id' => $job->id,
                            'notifiable_type' => Notification::JOB_NOTIFIABLE_TYPE,
                        ],
                        [
                            'type' => $this->convertToNotificationType($applicant->application->status),
                        ]
                    );
                    $notification->receivers()->attach($applicant->id, ['is_read' => false]);
                }
            }
        }

        $this->enableForeignKeys();
    }

    /**
     * @param mixed $jobApplicationType
     * @return mixed
     */
    public function convertToNotificationType($jobApplicationType)
    {
        $type = null;
        switch ($jobApplicationType) {
            case JobApplication::STATUS_DONE:
                $type = Notification::JOB_DONE_TYPE;
                break;

            default:
                $type = Notification::JOB_APPLY_TYPE;
                break;
        }

        return $type;
    }
}
