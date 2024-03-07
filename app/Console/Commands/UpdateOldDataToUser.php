<?php

namespace App\Console\Commands;

use App\Domains\OldSystemUser\Services\OldSystemUserService;
use Exception;
use Illuminate\Console\Command;

class UpdateOldDataToUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:update-column {column*}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update old data to users';

    /** @var OldSystemUserService */
    protected $oldSystemUserService;

    /**
     * Create a new command instance.
     *
     * @param OldSystemUserService $oldSystemUserService
     * @return void
     */
    public function __construct(OldSystemUserService $oldSystemUserService)
    {
        parent::__construct();
        $this->oldSystemUserService = $oldSystemUserService;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        try {
            $this->info('Processing...' . PHP_EOL);
            $this->oldSystemUserService->dataMigrationByColumns($this->argument('column'));
            $this->info('Completed!' . PHP_EOL);
            return 0;
        } catch (Exception $e) {
            $this->error('Error: ' . $e->getMessage() . PHP_EOL);
        }
    }
}
