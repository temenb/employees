<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Word extends Model
{
    use HasFactory;
    
    /**
     * 
     * @return MorphToMany
     */
    public function users(): MorphToMany
    {    
        return $this->morphToMany(User::class, 'taggable');
    }
}
