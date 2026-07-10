<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NatXNotification extends Model
{
    use HasFactory;

    protected $table = 'natx_notifications';

    protected $fillable = [
        'title',
        'type',
        'description',
        'date',
        'upto_date',
        'is_active',
        'created_by',
        'user_id',
    ];

    protected $casts = [
        'date' => 'date',
        'upto_date' => 'date',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function reads()
    {
        return $this->hasMany(NatXNotificationRead::class, 'natx_notification_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Only notifications still within upto_date are shown in the API.
     */
    public function scopeVisible($query)
    {
        return $query->active()
            ->whereNotNull('upto_date')
            ->whereDate('upto_date', '>=', now()->toDateString());
    }

    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }
}
