<?php

namespace Azit\Ddd\Helper;

use Illuminate\Support\Carbon;

/**
 * DateHelper
 */
class DateHelper {

    /**
     * Obtiene la fecha y hora actual
     * YYYY-m-d H:m:s
     * @return string
     */
    public static function getDateTime(): string {
        $date = Carbon::now();
        return $date->toDateTimeString();
    }

    /**
     * Obtiene la fecha
     * YYYY-m-d H:m:s
     * @return string
     */
    public static function getDate(): string {
        $date = Carbon::now();
        return $date -> toDateString();
    }

    /**
     * Convierte un string a un objecto carbon
     * @param string $date
     * @return Carbon
     */
    public static function parseCarbon(string $date) : Carbon{
        return Carbon::parse($date);
    }

    /**
     * Retorna días posteriores a la fecha actual
     * @param int $day
     * @return string
     */
    public static function getDateTimeSubDay(int $day): string {
        $date = Carbon::now();
        return $date -> subDays($day) -> toDateTimeString();
    }

    /**
     * Obtiene el año actual
     * @return string
     */
    public static function getDateYear(): string {
        $date =  Carbon::now();
        return $date -> year;
    }

}
