<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use App\Http\Requests\Contracts\StoreContractRequest;
use App\Http\Requests\Contracts\UpdateContractRequest;
use App\Services\Contract\ContractService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ContractController extends Controller
{

    private function getContractStatus($data): string {

        return ContractService::setContractStatusValue($data);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $contracts = Contract::query()
            ->whereHas('unit.property.asset', function ($q) {
                $q->where('manager_id', Auth::id());
            })
            ->with([
                'unit.property.asset',
                'tenant:id,first_name,last_name,phone_number,email'
            ])
            ->latest('beginning_date')
            ->get();

        return view('contracts.index', compact('contracts'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        return view('contracts.create');
    }

    /**
     * Store a newly created resource in storage.
     * ['tenant_id', 'beginning_date', 'end_date', 'total_amount', 'payment_plan', 'status', 'ended_at', 'unit_id'];
     */
    public function store(StoreContractRequest $request)
    {
        //
        $data = $request->validated();
        $data['status'] = self::getContractStatus($data);

        // contract model may throwes an error when vaiolating some rules
        try {

            Contract::create($data); 

        } catch (\InvalidArgumentException $e) {

            return back()->withErrors(['contract' => $e->getMessage()])->withInput();
        }

        return redirect()->route('contracts.index')->with('success', 'تم إضافة العقد بنجاح');
    }

    /**
     * Display the specified resource.
     */
    public function show(Contract $contract)
    {
        // Eager load all required relationships to avoid N+1 queries
        $contract->load([
            'tenant:id,first_name,last_name,phone_number,email,national_id,nationality',
            'unit.property.asset',
            'payments' => function($query) {
                $query->orderBy('payment_number', 'asc');
            }
        ]);

        return view('contracts.show', compact('contract'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Contract $contract)
    {
        //
        return view('contracts.edit', compact('contract'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateContractRequest $request, Contract $contract)
    {
        //
        $data = $request->validated();

        // contract model may throwes an error when vaiolating some rules
        try {

            $contract->update($data);

        } catch (\InvalidArgumentException $e) {

            return back()->withErrors(['contract' => $e->getMessage()])->withInput();
        }

        return redirect()->route('contracts.index')->with('success', 'تم تحديث العقد بنجاح');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Contract $contract)
    {
        //

        DB::transaction(function () use ($contract) {
            $contract->update([
                'ended_at' => now(),
                'status'   => 'terminated',
            ]);
        
            $contract->payments()
                ->where('status', 'pending')
                ->whereDate('due_date', '>', now())
                ->update([
                    'status'      => 'cancelled'
                ]);
        
            $contract->delete();
        });

        return redirect()->route('contracts.index')->with('success', 'تم حذف العقد بنجاح');
    }
}
