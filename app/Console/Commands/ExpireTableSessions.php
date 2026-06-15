<?php

namespace App\Console\Commands;

use App\Models\TableSession;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ExpireTableSessions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sessions:expire';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Expire table sessions that have passed their expiration time';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking for expired sessions...');

        $expiredSessions = TableSession::expired()->get();

        if ($expiredSessions->isEmpty()) {
            $this->info('No expired sessions found.');
            return 0;
        }

        $count = 0;
        foreach ($expiredSessions as $session) {
            $session->expire();
            $count++;
            
            $this->line("Expired session #{$session->id} for table #{$session->table->number}");
            
            Log::info('Session auto-expired by command', [
                'session_id' => $session->id,
                'table_id' => $session->table_id,
                'table_number' => $session->table->number,
            ]);
        }

        $this->info("Successfully expired {$count} session(s).");
        return 0;
    }
}
