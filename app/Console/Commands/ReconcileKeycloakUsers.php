<?php

namespace App\Console\Commands;

use App\Models\KeycloakUserSync;
use App\Services\KeycloakUserSyncService;
use Illuminate\Console\Command;

class ReconcileKeycloakUsers extends Command
{
    protected $signature = 'keycloak:reconcile-users';

    protected $description = 'Reconcile unfinished Keycloak identity updates without overwriting newer remote data';

    public function handle(KeycloakUserSyncService $service): int
    {
        $failed = false;
        KeycloakUserSync::where(function ($q) {
            $q->where('status', 'reconciliation_required')->orWhere(function ($pending) {
                $pending->where('status', 'pending')->where('updated_at', '<', now()->subMinutes(5));
            });
        })->orderBy('id')->chunkById(50, function ($syncs) use ($service, &$failed) {
            foreach ($syncs as $sync) {
                try {
                    if (! $service->reconcile($sync)) {
                        $failed = true;
                        $this->warn('Review required: '.$sync->id);
                    } else {
                        $this->info('Synced: '.$sync->id);
                    }
                } catch (\Throwable $exception) {
                    $failed = true;
                    $this->warn('Synchronization pending: '.$sync->id);
                }
            }
        });

        return $failed ? self::FAILURE : self::SUCCESS;
    }
}
