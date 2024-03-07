<?php

namespace App\Console\Commands;

use App\Domains\Escrow\Services\EscrowService;
use Exception;
use Illuminate\Console\Command;

class RegisterEscrowWebhook extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'escrow:register-webhook {--refresh : Remove all registered urls and re-register.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Register Escrow webhook.';

    /**
     * @var EscrowService
     */
    protected $escrowService;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(EscrowService $escrowService)
    {
        parent::__construct();
        $this->escrowService = $escrowService;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        try {
            $this->line('Processing...' . PHP_EOL);
            if ($this->option('refresh')) {
                throw_if(!$this->escrowService->refreshAndRegisterWebhook(), Exception::class, 'register failed!');
            } else {
                throw_if(!$this->escrowService->addingWebhookIfNotExists(), Exception::class, 'register failed!');
            }

            $this->info('Register success!' . PHP_EOL);
            $this->call('escrow:webhook-registered');
        } catch (Exception $e) {
            $this->error('Register error: ' . $e->getMessage() . PHP_EOL);
        }
    }
}
