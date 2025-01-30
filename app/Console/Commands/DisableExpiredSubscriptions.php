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
    protected $description = 'Deshabilita la suscripcion y tecnico al momento que la fecha se haya vencido.';

    /**
     * The console command description.
     *
     * @var string
     */

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $result= app(SubcritionMutations::class)->disableExpirateSuscription();
        $this->info($result['message']."Total desactivadas. " . $result['count']);
    }
}
