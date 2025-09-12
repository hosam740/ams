<?php

namespace App\Http\Controllers;

use App\Models\Tenants;
use App\Http\Requests\Tenants\StoreTenantRequest;
use App\Http\Requests\Tenants\UpdateTenantRequest;
use Illuminate\Support\Facades\Auth;


class TenantController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {


        $tenants = Tenants::all();

        //                  to fech tenants with active contract only 
        // $tenants = Tenants::whereHas('contracts', function ($q) {
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

        Tenants::create($data);

        return redirect()->route('tenants.index')->with('success', 'تم إضافة المستأجر بنجاح');
    }

    /**
     * Display the specified resource.
     */
    public function show(Tenants $tenants)
    {
        //
        return view('tenants.show', compact('tenants'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Tenants $tenants)
    {
        //
        return view('tenants.edit', compact('tenants'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTenantRequest $request, Tenants $tenants)
    {
        //
        $data = $request->validated();

        $tenants->update($data);

        return redirect()->route('tenants.index')->with('success', 'تم تحديث المستأجر بنجاح');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Tenants $tenants)
    {
        //
        if ($tenants->contracts()->where('active', true)->exists()) {
            return back()->withErrors([
                'tenant' => 'لا يمكن حذف المستأجر لوجود عقد نشط مرتبط به.'
            ]);
        }

        $tenants->delete();

        return redirect()->route('tenants.index')->with('success', 'تم حذف المستأجر بنجاح');
    }
}
