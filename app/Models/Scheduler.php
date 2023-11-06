<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Scheduler extends Model
{
    use HasFactory, SoftDeletes;
    
    protected $fillable = ['employee_id', 'day', 'from', 'to'];
    
    public function employee(): HasOne
    {
        return $this->hasOne(Employee::class);
    }
}
