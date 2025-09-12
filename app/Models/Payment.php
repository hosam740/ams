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
        'notes'
    ];


    protected $casts = ['due_date' => 'date'];

    

    protected static $status_values = ['pending', 'paid', 'overdue', 'cancelled'];

    // Validating status input
    public function setStatusAttribute($value)
    {
        if (!in_array($value, self::$status_values)) {
            throw new \InvalidArgumentException("Invalid status value: {$value} in payment model.");
        }

        $this->attributes['status'] = $value;
    }

    // contract/payment relationship -> contract has many payments
    public function contract(): BelongsTo
    {
        return $this->belongsTo(Contract::class, 'contract_id');
    }
}
