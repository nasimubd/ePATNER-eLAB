<?php

namespace App\Console\Commands;

use App\Models\Business;
use Illuminate\Console\Command;

class MarkExpiredBusinessesInactive extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'businesses:mark-expired-inactive';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Mark businesses with expired subscriptions as inactive';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking for expired business subscriptions...');

        // Find businesses that have expired subscriptions but are still marked as active
        $expiredBusinesses = Business::where('is_active', true)
            ->whereNotNull('due_date')
            ->where('due_date', '<', now())
            ->get();

        if ($expiredBusinesses->isEmpty()) {
            $this->info('No expired businesses found.');
            return;
        }

        $count = 0;
        foreach ($expiredBusinesses as $business) {
            $business->deactivateSubscription();
            $this->line("Marked '{$business->hospital_name}' as inactive (expired: {$business->due_date->format('Y-m-d')})");
            $count++;
        }

        $this->info("Successfully marked {$count} business(es) as inactive.");
    }
}
