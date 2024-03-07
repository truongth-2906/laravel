<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Domains\OldSystemUser\Services\OldSystemUserService;

class MoveUserFromOldSystem extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate:user';

    protected OldSystemUserService $oldSystemUserService;

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create user from old system';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(OldSystemUserService $oldSystemUserService)
    {
        parent::__construct();
        $this->oldSystemUserService = $oldSystemUserService;
    }

    /**
     * @return bool
     */
    public function handle()
    {
        $this->info('Converting data user...' . PHP_EOL);
        return $this->oldSystemUserService->createUserFromOldSystem();
    }
}
