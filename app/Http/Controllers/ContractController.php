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


        $contracts = Contract::all();

        // //
        // $contracts = Contract::where('active', true)
        //         ->whereRelation('unit.property.asset', 'manager_id', Auth::id())
        //         ->with('unit.property.asset')
        //         ->paginate(15);


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
        //
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
