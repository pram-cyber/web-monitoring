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
        'is_active',
        'tinggi_sampah',
        'sisa_ruang'
    ];

    protected $appends = ['is_connected'];

    public function getIsConnectedAttribute()
    {
        // Terhubung jika data trash bin diperbarui (menerima data dari IoT) kurang dari 30 detik yang lalu
        return $this->updated_at && now()->lt($this->updated_at->addSeconds(30));
    }

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