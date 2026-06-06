<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    protected $fillable = [
        'user_id',
        'trash_bin_id',
        'type',
        'description',
        'status',
    ];

    public function trashBin()
    {
        return $this->belongsTo(TrashBin::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}