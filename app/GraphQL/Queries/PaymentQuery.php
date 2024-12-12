<?php declare(strict_types=1);

namespace App\GraphQL\Queries;

use App\Models\Tecnico;
use App\Models\Pago;
use Carbon\Carbon;

class PaymentQuery
{
    protected $now;
    public function __construct(){
        $this->now = Carbon::now();
    }

    public function getsPaymentTechnician($root,array $args){
        $paymentTechnicianData = $args['requestPayment'];

    }
}
