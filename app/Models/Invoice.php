<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\PaymentLink;

class Invoice extends Model
{
    use HasFactory, SoftDeletes;

    /** Gross add-on when student opted for mobile at conversion (must match lead convert form). */
    public const NEED_MOBILE_ADDON_GROSS = 1000.0;

    /** Sentinel for taxInvoiceLineTotal() EduMaster mobile line (not a DB column). */
    public const TAX_LINE_FEE_HEAD_MOBILE = 'need_mobile_addon';

    protected $fillable = [
        'invoice_number',
        'invoice_type',
        'course_id',
        'batch_id',
        'student_id',
        'total_amount',
        'discount_amount',
        'fee_pg_amount',
        'fee_ug_amount',
        'fee_plustwo_amount',
        'fee_sslc_amount',
        'paid_amount',
        'status',
        'invoice_date',
        'previous_balance',
        'service_name',
        'service_amount',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'fee_pg_amount' => 'decimal:2',
        'fee_ug_amount' => 'decimal:2',
        'fee_plustwo_amount' => 'decimal:2',
        'fee_sslc_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'previous_balance' => 'decimal:2',
        'service_amount' => 'decimal:2',
        'invoice_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    // Relationships
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function batch()
    {
        return $this->belongsTo(Batch::class);
    }

    public function student()
    {
        return $this->belongsTo(ConvertedLead::class, 'student_id');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function paymentLinks()
    {
        return $this->hasMany(PaymentLink::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function deletedBy()
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }


    // Accessors
    public function getNetAmountAttribute(): float
    {
        $total = (float) $this->total_amount;
        $discount = (float) ($this->discount_amount ?? 0);

        return max(0, round($total - $discount, 2));
    }

    public function getPendingAmountAttribute(): float
    {
        return max(0, round($this->net_amount - (float) $this->paid_amount, 2));
    }

    public function getCurrentBalanceAttribute(): float
    {
        return max(0, round($this->net_amount - (float) $this->paid_amount, 2));
    }

    /**
     * Amount for tax invoice / receipts (after discount, no separate discount line).
     * For EduMaster (course 23) with a fee head, applies discount pro-rata to that head.
     */
    public function taxInvoiceLineTotal(?string $feeHeadColumn = null): float
    {
        $net = $this->net_amount;
        $total = (float) $this->total_amount;

        if ($feeHeadColumn && (int) ($this->course_id ?? 0) === 23) {
            if ($feeHeadColumn === self::TAX_LINE_FEE_HEAD_MOBILE) {
                $head = self::NEED_MOBILE_ADDON_GROSS;
            } else {
                $head = (float) ($this->{$feeHeadColumn} ?? 0);
            }
            if ($total <= 0) {
                return max(0, round($head, 2));
            }

            return max(0, round($head * ($net / $total), 2));
        }

        return $net;
    }

    public function hasNeedMobileAddon(): bool
    {
        return $this->invoice_type === 'course'
            && $this->student
            && (bool) $this->student->need_mobile;
    }

    public function mobileAddonGrossAmount(): float
    {
        return $this->hasNeedMobileAddon() ? self::NEED_MOBILE_ADDON_GROSS : 0.0;
    }

    /**
     * Mobile line share of net payable (discount applied pro-rata vs invoice total).
     */
    public function mobileNetAmount(): float
    {
        if (!$this->hasNeedMobileAddon()) {
            return 0.0;
        }
        $gross = self::NEED_MOBILE_ADDON_GROSS;
        $total = (float) $this->total_amount;
        if ($total <= 0) {
            return 0.0;
        }

        return round($gross * ($this->net_amount / $total), 2);
    }

    public function courseNetExcludingMobile(): float
    {
        return max(0, round($this->net_amount - $this->mobileNetAmount(), 2));
    }

    /**
     * Split a net line amount (e.g. tax-invoice line for this payment) into course vs mobile.
     *
     * @return array{course: float, mobile: float}
     */
    public function splitLineAmountForMobile(float $lineNetTotal): array
    {
        $lineNetTotal = round(max(0, $lineNetTotal), 2);
        $mobileNet = $this->mobileNetAmount();
        $net = $this->net_amount;
        if ($mobileNet <= 0 || $net <= 0) {
            return ['course' => $lineNetTotal, 'mobile' => 0.0];
        }

        $mobileShare = round($lineNetTotal * ($mobileNet / $net), 2);
        $courseShare = round($lineNetTotal - $mobileShare, 2);

        return [
            'course' => max(0, $courseShare),
            'mobile' => max(0, $mobileShare),
        ];
    }

    // Scopes
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByStudent($query, $studentId)
    {
        return $query->where('student_id', $studentId);
    }

    /**
     * Next invoice number for the current month (INV + YYYYMM + 4-digit sequence).
     * Includes soft-deleted rows because invoice_number remains unique in the database.
     */
    public static function generateNextInvoiceNumber(): string
    {
        $prefix = 'INV';
        $year = now()->year;
        $month = now()->format('m');
        $pattern = $prefix . $year . $month;

        $lastSequence = (int) static::withTrashed()
            ->where('invoice_number', 'like', $pattern . '%')
            ->selectRaw('MAX(CAST(SUBSTRING(invoice_number, -4) AS UNSIGNED)) as seq')
            ->value('seq');

        return $pattern . str_pad($lastSequence + 1, 4, '0', STR_PAD_LEFT);
    }

    // Methods
    public function updateStatus()
    {
        if ($this->paid_amount == 0) {
            $this->status = 'Not Paid';
        } elseif ($this->paid_amount >= $this->net_amount) {
            $this->status = 'Fully Paid';
        } else {
            $this->status = 'Partially Paid';
        }
        
        $this->save();
    }

    public function recalculatePaidAmount()
    {
        // Calculate total paid amount from all approved payments
        $totalPaid = $this->payments()
            ->where('status', 'Approved')
            ->sum('amount_paid');
        
        // Calculate previous balance (net amount - paid amount)
        $this->previous_balance = $this->net_amount - $totalPaid;
        $this->paid_amount = $totalPaid;
        $this->save();
    }

    public function addPayment($amount, $paymentType, $transactionId = null, $fileUpload = null)
    {
        $previousBalance = $this->current_balance;
        
        $payment = $this->payments()->create([
            'amount_paid' => $amount,
            'payment_type' => $paymentType,
            'transaction_id' => $transactionId,
            'file_upload' => $fileUpload,
            'status' => 'Pending Approval',
            'created_by' => \App\Helpers\AuthHelper::getCurrentUserId(),
        ]);

        // Update invoice paid amount and status
        $this->paid_amount += $amount;
        $this->previous_balance = $previousBalance;
        $this->updateStatus();

        return $payment;
    }

    /**
     * Override the delete method to set deleted_by
     */
    public function delete()
    {
        $this->deleted_by = \App\Helpers\AuthHelper::getCurrentUserId();
        $this->save();
        
        return parent::delete();
    }
}
