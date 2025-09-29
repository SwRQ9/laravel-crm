<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityType extends Model
{
    protected $fillable = [
        'name', 'slug', 'category', 'point_value', 'weight', 'is_active'
    ];
}
