<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Models\Auth\Activity;
use Illuminate\Console\Command;

class ActivityClean extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'activity-log:clean';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up old records from the activity log.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $days = (int)config('activity-log.delete_records_older_than');

        if (!($days > 0)) {
            $this->info("Could not clean up the activity because the key 'delete_records_older_than' is not set in the config/activity-log.php file.");
            return;
        }

        $date = Carbon::now()->subDays($days)->format('Y-m-d H:i:s');
        $number = Activity::where('created_at', '<', $date)->delete();

        $this->info("Activity log cleaned up. {$number} record(s) were removed.");
    }
}
