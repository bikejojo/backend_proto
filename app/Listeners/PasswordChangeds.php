<?php

namespace App\Listeners;

use App\Events\PasswordChanged;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class PasswordChangeds
{
    /**
     * Create the event listener.
     */

    protected $now;
    public function __construct()
    {
        Carbon::setLocale('es');
        $this->now = Carbon::now();
    }

    /**
     * Handle the event.
     */
    public function handle(PasswordChanged $event): void
    {
        //
    }
}
