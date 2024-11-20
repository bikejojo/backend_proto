<?php

namespace App\Services;

class StateCatalog {
    const INTERNAL_CLIENT = 1;
    const EXTERNAL_CLIENT = 2;
    const USER_CLIENT = 2;
    const USER_TECHNICIAN=1;
    //general
    const STATUS_ACTIVE = 1;
    const STATUS_LOW = 0;
    // publicidad
    const STATUS_PUBLICITY_ACTIVE = 1;
    const STATUS_PUBLICITY_EXPIRATION = 0;
    const STATUS_PUBLICITY_CANCELED=2;
    //actividad
    
}
