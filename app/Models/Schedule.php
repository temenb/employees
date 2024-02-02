<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Enum\WeekDay;

class Schedule extends Model
{
    use HasFactory, SoftDeletes;
    
    protected $fillable = ['employee_id', 'day', 'from', 'to'];
    
    public function employee(): HasOne
    {
        return $this->hasOne(Employee::class);
    }
    
    static public function convertStringToTimestamp ($time) 
    {
        $_time = explode(':', $time);
        $timestamp = 60*60*intval($_time[0]) + 60*intval($_time[1]?? 0);
        return $timestamp;
    }
    
    static public function convertTimestampToString ($timestamp)
    {
        $hours = intdiv($timestamp, 60*60);
        $minutes = ($timestamp % (60*60))/60;
        $time = $num_padded = sprintf("%02d", $hours) . ':' . sprintf("%02d", $minutes);
        return $time;
    }
    
    static public function createCustom($employeeId, $day, $from, $to)
    {
        $timestampFrom = Schedule::convertStringToTimestamp($from);
        $timestampTo = Schedule::convertStringToTimestamp($to);
        $timestampTo = $timestampTo? $timestampTo: Schedule::convertStringToTimestamp('24:00');
        
        $schedule = Schedule::make();
        $schedule->employee_id = $employeeId;
        $schedule->day = $day;
        $schedule->from = $timestampFrom;
        if ($timestampFrom <= $timestampTo) {
            $schedule->to = $timestampTo;
            $schedule->save();            
        } else {
            $schedule->to = Schedule::convertStringToTimestamp('24:00');
            $schedule->save();            
            
            self::createCustom($employeeId, ($day + 1)%count(WeekDay::DAYS), '00:00', $to);
        }
    }
}
