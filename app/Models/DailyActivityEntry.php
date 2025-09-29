<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyActivityEntry extends Model
{
    use HasFactory;

    protected $fillable = [
        'daily_activity_id',
        'activity_type_id',
        'value',
    ];

    /**
     * Each entry belongs to a daily activity (one form submission).
     */
    public function dailyActivity()
    {
        return $this->belongsTo(DailyActivity::class);
    }

    /**
     * Each entry also belongs to an activity type (calls, cases closed, etc).
     */
    public function activityType()
    {
        return $this->belongsTo(ActivityType::class);
    }
}
