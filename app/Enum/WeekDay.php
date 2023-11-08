<?php
namespace App\Enum;

class WeekDay
{
    const DAYS = [
        'Sun',
        'Mon',
        'Tue',
        'Wed',
        'Thu',
        'Fri',
        'Sat',
    ];
    
    static public function convertIntsToString(array $nums) {
        $days = [];
        foreach($nums as $num) {
            $days[] = self::DAYS[$num];
        }
        return implode(', ', $days);
    }
}