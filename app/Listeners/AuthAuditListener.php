<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Traits\LogsAudit;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Support\Facades\Log;

class AuthAuditListener
{
    use LogsAudit;
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle($event): void
    {
        if ($event instanceof Login) {
            $this->logAuthAction($event->user, 'login');
        } elseif ($event instanceof Logout) {
            $this->logAuthAction($event->user, 'logout');
        }
    }

    /**
     * Log authentication actions to audit_logs.
     */
    protected function logAuthAction($user, string $actionType): void
    {
        try {
            $this->logAudit(
                model: $user,
                actionType: $actionType
            );
        } catch (\Exception $e) {
            Log::error("Failed to log auth action: {$actionType} for user ID {$user->employeeID}", ['error' => $e->getMessage()]);
        }
    }
}
