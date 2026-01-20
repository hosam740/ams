<?php

namespace App\Http\Controllers;

use App\Http\Requests\Payment\CancelPaymentRequest;
use App\Http\Requests\Payment\MarkPaymentAsPaidRequest;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $payments = Payment::query()
            ->whereHas('contract.unit.property.asset', function ($q) {
                $q->where('manager_id', Auth::id());
            })
            ->with([
                'contract.unit:id,property_id,name',
                'contract.unit.property:id,city,neighborhood',
                'contract.unit.property.asset:id,name',
                'contract.tenant:id,first_name,last_name,phone_number,email'
            ])
            ->latest('due_date')
            ->get();

        return view('payments.index', compact('payments'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Payment $payment)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Payment $payment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Payment $payment)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Payment $payment)
    {
        //
    }

    /**
     * Mark a payment as paid.
     */
    public function markAsPaid(MarkPaymentAsPaidRequest $request, Payment $payment)
    {
        $payment->update([
            'status' => 'paid',
            'paid_amount' => $request->paid_amount,
            'paid_date' => $request->paid_date,
            'payment_method' => $request->payment_method,
            'received_by' => Auth::id(),
            'notes' => $request->notes ?? $payment->notes,
        ]);

        return back()->with('success', 'تم تسجيل السداد بنجاح');
    }

    /**
     * Cancel a payment.
     */
    public function cancel(CancelPaymentRequest $request, Payment $payment)
    {
        $payment->update([
            'status' => 'cancelled',
            'notes' => $request->notes ?? $payment->notes,
        ]);

        return back()->with('success', 'تم إلغاء الدفعة بنجاح');
    }
}
