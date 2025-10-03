<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use App\Http\Requests\Tenant\StoreTenantRequest;
use App\Http\Requests\Tenant\UpdateTenantRequest;
use Illuminate\Support\Facades\Auth;


class TenantController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {


        $tenants = Tenant::all();

        //                  to fech tenants with active contract only 
        // $tenants = Tenant::whereHas('contracts', function ($q) {
        //     $q->where('active', true)

        //     // fech tenants with the condition 
        //       ->whereHas('unit.property.asset', fn($a) => 
        //           $a->where('manager_id', Auth::id()));
        // })
        // //  with tenants fech only active contracts
        // ->with(['contracts' => function ($q) {
        //     $q->where('active', true)->with('unit.property.asset');
        // }])
        // ->get();


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
    public function show(Tenant $tenant)
    {
        //
        return view('tenants.show', compact('tenant'));
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
