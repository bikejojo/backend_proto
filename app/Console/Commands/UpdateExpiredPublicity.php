<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use App\Models\Publicidad;
use App\Services\StateCatalog;

class UpdateExpiredPublicity extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-expired-publicity';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update the status of expired posts';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        //
        $now = Carbon::now();
        $expiredPublicity = Publicidad::where('finishDate' , '<' , $now )
        ->where('status','!=',StateCatalog::STATUS_PUBLICITY_EXPIRATION)
        ->update(['status'=>StateCatalog::STATUS_PUBLICITY_EXPIRATION]);

        if($expiredPublicity > 0 ){
            $this->info("Se actualizaron $expiredPublicity publicaciones vencidas.");
        }else{
            $this->info("No hay publicaciones vencidas para actualizar.");
        }
    }
}
// comando para actualizar php artisan update:Expired
