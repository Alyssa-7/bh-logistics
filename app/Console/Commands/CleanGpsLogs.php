<?php

namespace App\Console\Commands;

use App\Models\GpsLog;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CleanGpsLogs extends Command
{
    protected $signature = 'gps:clean-logs';
    protected $description = 'Delete GPS logs older than 30 days';

    public function handle(): int
    {
        $deleted = GpsLog::where('recorded_at', '<', Carbon::now()->subDays(30))->delete();
        $this->info("Deleted {$deleted} old GPS log(s).");
        return Command::SUCCESS;
    }
}
