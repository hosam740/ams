<?php

namespace App\Http\Controllers;

use App\Models\assets\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UnitController extends Controller
{


    private static function storeRules(): array{

        $rules = [
            'name'         => 'required|string|max:255',
            'type'         => ['required', Rule::in(Unit::getTypeValues())],
            'description'  => ['required', 'string', 'max:1000'],
            'area'         => ['required', 'numeric', 'min:1'],
            'status'       => ['required', Rule::in(Unit::getStatusValues())],
            'property_id'  => ['required', 'exists:properties,id'],
        ];

        return $rules;
    }

    private static function updateRules(): array{

        $rules = [
            'name'         => 'required|string|max:255',
            'type'         => ['sometimes', Rule::in(Unit::getTypeValues())],
            'description'  => ['sometimes', 'string', 'max:1000'],
            'area'         => ['sometimes', 'numeric', 'min:1'],
            'status'       => ['sometimes', Rule::in(Unit::getStatusValues())],
        ];

        return $rules;
    }



    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $units = Unit::query()
            ->whereHas('property.asset', function ($q) {
                $q->where('manager_id', Auth::id());})
                ->with(['property.asset'])->get();


        return view('units.index', compact('units'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        return view('units.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        $validated = $request->validate(self::storeRules());

        Unit::create($validated);

        return redirect()->route('units.index')->with('success', 'تم إضافة الوحدة بنجاح');
    }

    /**
     * Display the specified resource.
     */
    public function show(Unit $unit)
    {
        //
        return view('units.show', compact('unit'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Unit $unit)
    {
        //
        return view('units.edit', compact('unit'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Unit $unit)
    {
        //
        $validated = $request->validate(self::updateRules());

        $unit->update($validated);

        return redirect()->route('units.index')->with('success', 'تم تحديث الوحدة بنجاح');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Unit $unit)
    {
        //
        $unit->delete();

        return redirect()->route('units.index')->with('success', 'تم حذف الوحدة بنجاح');
    }
}
