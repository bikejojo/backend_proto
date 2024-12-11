<?php
namespace App\Helpers;

use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;



class StatusHelper{
    //
    const STATE_PENDING = 1;
    const STATE_ACCEPT = 2;
    const STATE_CANCEL = 3;
    //ordenes
    const ORDER_BY_RECENT = 4;
    const ORDER_BY_OLD = 5;
    const ORDER_NAME_RECENT = 'mas reciente';
    const ORDER_NAME_OLD = 'mas antiguo';
    //
    const DATE_TODAY =6;
    const DATE_WEEK =7;
    const DATE_MONTH =8;
    const DATE_ALL =9;
    const DATE_NAME_WEEK ='esta semana';
    const DATE_NAME_MONTH ='este mes';
    const DATE_NAME_TODAY ='hoy';
    const DATE_NAME_ALL ='todos';

    public static function applyOrderFilter($query, $orderType){
        switch ($orderType) {
            case self::ORDER_BY_RECENT:
                $query->orderBy('registrationDateTime', 'DESC');
                break;
            case self::ORDER_BY_OLD:
                $query->orderBy('registrationDateTime', 'ASC');
                break;
        }
        return $query;
    }
    //por fecha
    public static function applyDateFilter($query,$dateType){
        $now = Carbon::now();
        switch ($dateType) {
            case self::DATE_TODAY:
                $query->whereDate('registrationDateTime', $now->toDateString());
                break;
            case self::DATE_WEEK:
                $query->whereBetween('registrationDateTime', [$now->startOfWeek()->format('Y-m-d H:i'), $now->endOfWeek()->format('Y-m-d H:i')]);
                break;
            case self::DATE_MONTH:
                $query->whereMonth('registrationDateTime', $now->month);
                break;
        }
        return $query;
    }
}
