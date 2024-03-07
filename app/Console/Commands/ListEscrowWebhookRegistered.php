<?php

namespace App\Console\Commands;

use App\Domains\Escrow\Services\EscrowService;
use Exception;
use Illuminate\Console\Command;

class ListEscrowWebhookRegistered extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'escrow:webhook-registered';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List Escrow Webhook Registered';

    /**
     * @var EscrowService
     */
    protected $escrowService;

    /**
     * Create a new command instance.
     *
     * @param EscrowService $escrowService
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
            $this->info('Urls registered: ' . PHP_EOL);
            $urls = $this->escrowService->listWebhookRegistered();
            if ($urls->isNotEmpty()) {
                $urls->each(function($item) {
                    $this->info("   - " . $item . PHP_EOL);
                });
            } else {
                $this->warn('Empty' . PHP_EOL);
            }
        } catch (Exception $e) {
            $this->error('  - ' . $e->getMessage() . PHP_EOL);
        }
    }
}
