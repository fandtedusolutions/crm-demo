<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NatXWorkStatus extends Model
{
    public const SLOT_MORNING = 'morning';

    public const SLOT_AFTERNOON = 'afternoon';

    public const SLOT_EVENING = 'evening';

    public const SLOTS = [
        self::SLOT_MORNING,
        self::SLOT_AFTERNOON,
        self::SLOT_EVENING,
    ];

    protected $table = 'natx_work_status';

    protected $fillable = [
        'user_id',
        'work_date',
        'slot',
        'updated_at_ms',
        'completed_at',
        'device_id',
    ];

    protected $casts = [
        'work_date' => 'date',
        'completed_at' => 'datetime',
        'updated_at_ms' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function formatUpdatedAtIso(?Carbon $dateTime): ?string
    {
        if ($dateTime === null) {
            return null;
        }

        return $dateTime
            ->copy()
            ->timezone(config('app.timezone', 'Asia/Kolkata'))
            ->format('Y-m-d\TH:i:s.vP');
    }

    public static function normalizeEpochMilliseconds(?int $value): ?int
    {
        if ($value === null || $value <= 0) {
            return null;
        }

        if ($value < 1_000_000_000_000) {
            return $value * 1000;
        }

        return $value;
    }

    public function toApiEntry(): array
    {
        return [
            'slot' => $this->slot,
            'date' => $this->work_date->format('Y-m-d'),
            'updated_at' => self::formatUpdatedAtIso($this->completed_at),
            'updated_at_ms' => (int) $this->updated_at_ms,
        ];
    }

    public function completionTimeDisplay(): string
    {
        if ($this->completed_at === null) {
            return '-';
        }

        return $this->completed_at
            ->copy()
            ->timezone(config('app.timezone', 'Asia/Kolkata'))
            ->format('h:i A');
    }
}
