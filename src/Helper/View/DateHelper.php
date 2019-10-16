<?php

namespace App\Helper\View;

use Jenssegers\Date\Date;

class DateHelper
{
    public static function relative(\DateTime $dateTime): string
    {
        $date = new Date($dateTime->getTimestamp(), $dateTime->getTimezone());

        return $date->diffForHumans();
    }
}
