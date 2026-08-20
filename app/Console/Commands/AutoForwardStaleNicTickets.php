<?php

namespace App\Console\Commands;

use App\Models\FreshHelpdesk;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class AutoForwardStaleNicTickets extends Command
{
    protected $signature = 'helpdesk:auto-forward-nic-tickets';
    protected $description = 'Auto forward Fresh Helpdesk tickets to a Senior Developer if NIC Admin has not acted within 2 minutes of receiving them';

    public function handle()
    {
        try {
            $forwardedIds = FreshHelpdesk::autoForwardStaleNicTickets(2);

            if (count($forwardedIds) > 0) {
                $this->info('Auto forwarded '.count($forwardedIds).' ticket(s) to Senior Developer.');
            }
        } catch (\Throwable $e) {
            Log::error('Auto forward stale NIC tickets failed: '.$e->getMessage());
            $this->error($e->getMessage());
        }
    }
}
