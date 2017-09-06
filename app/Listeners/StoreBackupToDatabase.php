<?php

namespace App\Listeners;

use App\Models\Backup\Backup;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Spatie\Backup\Events\BackupWasSuccessful;

class StoreBackupToDatabase /*implements ShouldQueue*/
{
    //use InteractsWithQueue;

    /**
     * Handle the event.
     *
     * @param BackupWasSuccessful $event
     * @return void
     */
    public function handle(BackupWasSuccessful $event)
    {
        $destination = $event->backupDestination;
        $backup = $destination->newestBackup();

        Backup::create([
            'name' => $destination->backupName(),
            'disk' => $destination->diskName(),
            'path' => $backup->path(),
            'date' => $backup->date(),
            'size' => $backup->size(),
        ]);
    }
}