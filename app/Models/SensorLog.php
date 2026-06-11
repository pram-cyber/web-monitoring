<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SensorLog extends Model
{
    protected $fillable = [
        'trash_bin_id',
        'distance_cm',
        'percentage',
        'status',
        'latitude',
        'longitude',
        'gps_accuracy',
        'is_history',
        'tinggi_sampah',
        'sisa_ruang'
    ];

    

    protected $casts = [
        'recorded_at' => 'datetime',
    ];

    public function trashBin()
    {
        return $this->belongsTo(TrashBin::class);
    }
}