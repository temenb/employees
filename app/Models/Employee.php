<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory, SoftDeletes;
    
    protected $fillable = ['name', 'telegram'];
    
    public function schedules(): HasMany
    {
        return $this->hasMany(Schedule::class)->orderBy('day', 'asc')->orderBy('from', 'asc');
    }
}
