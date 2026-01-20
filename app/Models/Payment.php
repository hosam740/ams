<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;


class Payment extends Model
{
    /** @use HasFactory<\Database\Factories\PaymentFactory> */
    use HasFactory;
    use SoftDeletes;

    

    protected $fillable = [
        'contract_id',
        'payment_number',
        'due_date',
        'amount',
        'status',
        'paid_date',
        'paid_amount',
        'notes',
        'payment_method',
        'received_by',
    ];

    protected $casts = ['due_date' => 'date'];


    /**
     * Number of days before due_date when payment status changes from 'pending' to 'due'.
     * After due_date passes, status changes to 'overdue'.
     */
    private const DAYS_BEFORE_OVERDUE = 7;

    
    /**
     * Valid payment methods.
     */
    private static $payment_methods = ['cash', 'bank_transfer', 'check'];

    

    protected static $status_values = ['pending', 'due', 'paid', 'overdue', 'cancelled'];

    /**
     * Statuses that allow marking as paid.
     */
    private static $payable_statuses = ['pending', 'due', 'overdue'];

    /**
     * Statuses that allow cancellation.
     */
    private static $cancellable_statuses = ['pending', 'due'];
    
    
    /**
     * Get the number of days before due_date when payment becomes 'due'.
     */
    public static function getDaysBeforeOverdue(): int
    {
        return self::DAYS_BEFORE_OVERDUE;
    }

    /**
     * Get valid payment methods.
     */
    public static function getPaymentMethods(): array
    {
        return self::$payment_methods;
    }

    /**
     * Get statuses that allow marking as paid.
     */
    public static function getPayableStatuses(): array
    {
        return self::$payable_statuses;
    }

    /**
     * Get statuses that allow cancellation.
     */
    public static function getCancellableStatuses(): array
    {
        return self::$cancellable_statuses;
    }

    /**
     * Check if this payment can be marked as paid.
     */
    public function canBeMarkedAsPaid(): bool
    {
        return in_array($this->status, self::$payable_statuses);
    }

    /**
     * Check if this payment can be cancelled.
     */
    public function canBeCancelled(): bool
    {
        return in_array($this->status, self::$cancellable_statuses);
    }

    /**
     * Validate status input.
     */
    public function setStatusAttribute($value)
    {
        if (!in_array($value, self::$status_values)) {
            throw new \InvalidArgumentException("Invalid status value: {$value} in payment model.");
        }

        $this->attributes['status'] = $value;
    }

    /**
     * Get the contract that owns this payment.
     */
    public function contract(): BelongsTo
    {
        return $this->belongsTo(Contract::class, 'contract_id');
    }

    /**
     * Get the user who received this payment.
     * This is the staff member who collected the payment from the tenant.
     */
    public function receivedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'received_by');
    }
}
