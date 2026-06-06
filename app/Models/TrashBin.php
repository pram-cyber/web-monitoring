<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrashBin extends Model
{
    protected $fillable = [
        'name',
        'location',
        'latitude',
        'longitude',
        'percentage',
        'distance_cm',
        'max_depth_cm',
        'status',
        'is_active'
    ];

    // Relasi ke SensorLog
    public function sensorLogs()
    {
        return $this->hasMany(SensorLog::class);
    }

    // Ambil log terbaru
    public function latestLog()
    {
        return $this->hasOne(SensorLog::class)->latestOfMany();
    }
}