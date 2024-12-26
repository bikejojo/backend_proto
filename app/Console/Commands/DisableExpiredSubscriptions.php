<?php

namespace App\Console\Commands;

use App\GraphQL\Mutations\SubcritionMutations;
use Illuminate\Console\Command;

class DisableExpiredSubscriptions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscriptions:disable-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Deshabilita suscripciones vencidas diariamente.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        app(SubcritionMutations::class)->disableExpirateSuscription();
    }
}
