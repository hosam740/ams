<?php

namespace App\Models;

use App\Models\assets\Unit;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Concerns\PaymentGenerationOptimized;
use Illuminate\Database\Eloquent\SoftDeletes;


class Contract extends Model
{
    use HasFactory;
    use SoftDeletes;
    use PaymentGenerationOptimized;

    protected $fillable = ['tenant_id', 'beginning_date', 'end_date', 'total_amount', 'payment_plan', 'active', 'ended_at', 'unit_id'];


    private static $contract_status_values =  ['pending', 'active', 'expired', 'terminated'];



    private static $payment_plan_values =  ['monthly', 'quarterly', 'triannual', 'semiannual', 'annually'];

    public function setStatusAttribute($value){

        if(!in_array($value, self::$contract_status_values)){

            throw new \InvalidArgumentException("Invalid contract status value: {$value} in contract model.");
        }

        $this->attributes['status'] = $value;
    }


    public function setPayment_planAttribute($value){

        if(!in_array($value, self::$payment_plan_values)){

            throw new \InvalidArgumentException("Invalid payment plan value: {$value} in contract model.");
        }

        $this->attributes['payment_plan'] = $value;
    }

    public static function getContractStatusValues(): array{

        return self::$contract_status_values;
    }

    public static function getPaymentPlanValues(): array{

        return self::$payment_plan_values;
    }



    // tenant/contract relationship
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class, 'tenant_id');
    }

    // contract/unit relationship
    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }

    // contract/payment relationship -> contract has many payments
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class, 'contract_id');
    }

    


    // this method runs automaticlly when creating, updating, saving a contract in the database. to ensure that no unit has two active contracts
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($contract) {
            static::validateUniqueActiveContract($contract);
        });

        static::saved(function ($contract) {
            // توليد عند الإنشاء الأول فقط إذا كان Active
            if ($contract->wasRecentlyCreated && $contract->active) {
                $contract->generatePayments();
                return;
            }

            // إعادة توليد عند تغيّر الحقول المؤثرة
            if ($contract->active && $contract->wasChanged(['beginning_date', 'end_date', 'total_amount', 'payment_plan'])) {
                $contract->regeneratePayments();
            }
        });
    }



    // this function throws an error when tring to add active/pending conrtact to a unit that has one
    private static function validateUniqueActiveContract($contract){

        if($contract->status == 'active'){

            $query = self::where('unit_id', $contract->unit_id)->whereIn('status', ['active', 'pending']);

            if($contract->exists){
                $query->where('id', '!=', $contract->id);
            }

            if($query->exists()){
                throw new \InvalidArgumentException(
                    "Contract with ID {$contract->id} cannot be saved! There is already an active contract for unit {$contract->unit_id}.");
            }
        }
        
        return true;
    }

    
    // private function generatePayments($startingPaymentNumber = 1){
        
    //     //                      returns array of date of payments
    //     $paymentDates = $this->calculatePaymentDates();

    //     $number_of_payments = count($paymentDates);

    //     $paymentAamount = $this->calculatePaymentAmount($number_of_payments);

    //     $payment_number = $startingPaymentNumber;
    //     foreach($paymentDates as $date){

    //         Payment::create([
    //             'contract_id' => $this->id,
    //             'payment_number' => $payment_number,
    //             'due_date' => $date,
    //             'amount' => $paymentAamount,
    //             'status' => 'pending'
    //         ]);

    //         $payment_number++;
    //     }
    // }


    // private function calculatePaymentDates(){

    //     $dates = [];

    //     $beginning_date = Carbon::parse($this->beginning_date);
    //     $end_date = Carbon::parse($this->end_date);

    //     $current_payment_date = $beginning_date->copy();

    //     //                     getPaymentPlan() method returns an assositive array
    //     $payment_plan = $this->getPaymentPlan();

    //     // lte --> less than or equal
    //     while($current_payment_date->lt($end_date)){

    //         $dates[] = $current_payment_date->copy();

    //         $current_payment_date->add($payment_plan['value'], $payment_plan['unit']);
    //     }

    //     return $dates;
    // }

    // private function getPaymentPlan(): array {

    //     return match($this->payment_plan){
    //         'monthly' => ['value' => 1, 'unit' => 'month'],
    //         'quarterly' => ['value' => 3, 'unit' => 'months'],
    //         'triannual' => ['value' => 4, 'unit' => 'months'],
    //         'semiannual' => ['value' => 6, 'unit' => 'months'],
    //         'annually' => ['value' => 1, 'unit' => 'year'],
    //         default => ['value' => 1, 'unit' => 'month']
    //     };
    // }


    // private function calculatePaymentAmount($number_of_payments): float{

    //     if ($number_of_payments <= 0) {
    //         throw new \InvalidArgumentException("The number of payments ({$number_of_payments}) must be greater than zero.\n (calculatePaymentAmount method in contract model)");
    //     }

    //     return round($this->total_amount / $number_of_payments, 2);
    // }


    // private function getDateInDays($beginning_date, $end_date){

    //     return $beginning_date->diffInDays($end_date) + 1; // شامل البداية والنهاية
    // }


    // private function regeneratePayments(){

    //     // last payment number that wont be regenerated
    //     $last_payment_number = $this->payments()->whereIn('status', ['paid', 'overdue'])->max('payment_number') ?? 0;

    //     $this->payments()->where('status', 'pending')->delete();

    //     $this->generatePayments($last_payment_number + 1);
    // }
}
