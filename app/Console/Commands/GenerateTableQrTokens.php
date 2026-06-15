<?php

namespace App\Console\Commands;

use App\Models\RestaurantTable;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class GenerateTableQrTokens extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tables:generate-qr {--force : Regenerate QR tokens for all tables}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate QR tokens for restaurant tables';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $force = $this->option('force');

        if ($force) {
            if (!$this->confirm('This will regenerate QR tokens for ALL tables. Continue?')) {
                $this->info('Operation cancelled.');
                return 0;
            }

            $tables = RestaurantTable::all();
            $this->info('Regenerating QR tokens for all tables...');
        } else {
            $tables = RestaurantTable::whereNull('qr_token')
                ->orWhere('qr_token', '')
                ->get();

            if ($tables->isEmpty()) {
                $this->info('All tables already have QR tokens.');
                return 0;
            }

            $this->info("Generating QR tokens for {$tables->count()} table(s)...");
        }

        $bar = $this->output->createProgressBar($tables->count());
        $bar->start();

        foreach ($tables as $table) {
            $table->qr_token = Str::random(32);
            $table->save();

            $qrUrl = route('qr.scan', ['qrToken' => $table->qr_token]);
            
            $this->newLine();
            $this->line("Table #{$table->number}:");
            $this->line("  Token: {$table->qr_token}");
            $this->line("  URL: {$qrUrl}");
            
            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);
        $this->info("Successfully generated QR tokens for {$tables->count()} table(s)!");
        
        return 0;
    }
}
