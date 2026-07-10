<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NatXNotificationRead extends Model
{
    use HasFactory;

    protected $table = 'natx_notification_reads';

    protected $fillable = [
        'natx_notification_id',
        'user_id',
        'read_at',
    ];

    protected $casts = [
        'read_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function notification()
    {
        return $this->belongsTo(NatXNotification::class, 'natx_notification_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
