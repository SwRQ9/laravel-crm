<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DailyActivity extends Model
{
    protected $fillable = [
        'user_id', 'date', 'activities', 'total_points', 'submitted_at'
    ];

    // Cast activities JSON to array
    protected $casts = [
        'activities' => 'array',
        'date' => 'date',
        'submitted_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function entries()
{
    return $this->hasMany(DailyActivityEntry::class);
}

public function getTotalPointsAttribute()
{
    return $this->entries()
        ->with('activityType')
        ->get()
        ->sum(fn($e) => $e->value * $e->activityType->point_value);
}

}
