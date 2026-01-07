<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use App\Http\Requests\Tenant\StoreTenantRequest;
use App\Http\Requests\Tenant\UpdateTenantRequest;
use App\Services\Contract\ContractService;
use Illuminate\Support\Facades\Auth;


class TenantController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tenants = Tenant::query()
            ->whereHas('contracts.unit.property.asset', function ($q) {
                $q->where('manager_id', Auth::id());
            })
            ->with([
                'contracts' => function ($q) {
                    $q->whereHas('unit.property.asset', function ($query) {
                            $query->where('manager_id', Auth::id());
                        })
                        ->with([
                            'unit:id,property_id,name',
                            'unit.property:id,city,neighborhood',
                            'unit.property.asset:id,name'
                        ])
                        ->latest('beginning_date');
                }
            ])
            ->withCount([
                'contracts as active_contracts_count' => function ($q) {
                    $q->where('status', 'active')
                      ->whereHas('unit.property.asset', function ($query) {
                          $query->where('manager_id', Auth::id());
                      });
                }
            ])
            ->get();

        return view('tenants.index', compact('tenants'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        return view('tenants.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTenantRequest $request)
    {
        // the validation rules are in StoreTenantRequest.php file, and the validated() method go to this file
        $data = $request->validated();

        Tenant::create($data);

        return redirect()->route('tenants.index')->with('success', 'تم إضافة المستأجر بنجاح');
    }

    /**
     * Display the specified resource.
     */
    public function show(Tenant $tenant, ContractService $contractService)
    {
        // Eager load all relationships to avoid N+1 queries
        $tenant->load([
            'contracts' => function ($query) {
                $query->whereHas('unit.property.asset', function ($q) {
                        $q->where('manager_id', Auth::id());
                    })
                    ->with([
                        'unit:id,property_id,name,type,status',
                        'unit.property:id,city,neighborhood',
                        'unit.property.asset:id,name',
                        'payments' => function ($q) {
                            $q->orderBy('due_date', 'asc');
                        }
                    ])
                    ->orderBy('beginning_date', 'desc');
            }
        ]);

        // Get the primary contract (active > pending > latest)
        $primaryContract = $tenant->contracts->firstWhere('status', 'active')
            ?? $tenant->contracts->firstWhere('status', 'pending')
            ?? $tenant->contracts->first();

        return view('tenants.show', compact('tenant', 'primaryContract'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Tenant $tenant)
    {
        //
        return view('tenants.edit', compact('tenant'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTenantRequest $request, Tenant $tenant)
    {
        //
        $data = $request->validated();

        $tenant->update($data);

        return redirect()->route('tenants.index')->with('success', 'تم تحديث المستأجر بنجاح');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Tenant $tenant)
    {
        //
        if ($tenant->contracts()->whereIn('status', ['active', 'pending'])->exists()) {
            return back()->withErrors([
                'tenant' => 'لا يمكن حذف المستأجر لوجود عقد نشط مرتبط به.'
            ]);
        }

        $tenant->delete();

        return redirect()->route('tenants.index')->with('success', 'تم حذف المستأجر بنجاح');
    }
}
