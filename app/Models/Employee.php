<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory, SoftDeletes;
    
    protected $fillable = ['name', 'telegram', 'suspended'];
    
    public function schedules(): HasMany
    {
        return $this->hasMany(Schedule::class)->orderBy('day', 'asc')->orderBy('from', 'asc');
    }
    
    public function schedulesAgregatedByDays(): array
    {
        $schedulers = $this->schedules;
        $_result = [];
        foreach ($schedulers as $scheduler) {
            $_result[$scheduler->from][$scheduler->to]['days'][] = $scheduler->day;
            $_result[$scheduler->from][$scheduler->to]['ids'][] = $scheduler->id;
        }
        
        $result = [];
        foreach ($_result as $from => $_data) {
            foreach ($_data as $to => $data) {
                $result[] = [
                    'ids' => $data['ids'],
                    'days' => $data['days'],
                    'from' => $from,
                    'to' => $to,
                ];
            }
        }

        return $result;
    }
}
