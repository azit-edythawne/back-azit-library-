<?php

namespace Azit\Ddd\Azit\Ddd\Helper;

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
     * Retorna días posteriores a la fecha actual
     * @param int $day
     * @return string
     */
    public static function getDateTimeSubDay(int $day): string {
        $date = Carbon::now();
        return $date->subDays($day)->toDateTimeString();
    }

    public static function getDateYear(): string {
        $date =  Carbon::now();
        return $date->year;
    }

}
