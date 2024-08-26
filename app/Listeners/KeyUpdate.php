<?php

namespace App\Listeners;

use App\Models\Key;
use App\Events\RegisterKeySubmit;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class KeyUpdate implements ShouldQueue
{
    /**
     * Handle the event.
     *
     * @param  \App\Events\RegisterKeySubmit  $event
     * @return void
     */
    public function handle(RegisterKeySubmit $event): void
    {
        $keyFromRequest = $event->user->key;

        // Cari kunci di tabel keys
        $keyRecord = Key::where('key', $keyFromRequest)->first();
        
        if ($keyRecord) {
            $keyRecord->update(['key_status' => 'dipakai']);
            Log::info('Key status updated to dipakai', ['key' => $keyRecord]);
        } else {
            Log::warning('Key not found', ['key' => $keyFromRequest]);
        }
    }
}
