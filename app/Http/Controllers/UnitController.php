<?php

namespace App\Http\Controllers;

use App\Models\assets\Unit;
use App\Http\Requests\Units\StoreUnitRequest;
use App\Http\Requests\Units\UpdateUnitRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UnitController extends Controller
{


    private static function getTypeValues(): array{

        return Unit::getTypeValues();
    }

    private static function getStatusValues(): array{

        return Unit::getStatusValues();
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
        $data = [
            'type_values' => self::getTypeValues(),
            'status_values' => self::getStatusValues(),
        ];
        return view('units.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUnitRequest $request)
    {
        //
        $validated = $request->validated();

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
        $data = ['type_values' => self::getTypeValues(), 'status_values' => self::getStatusValues()];

        return view('units.edit', array_merge(compact('unit'), $data));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUnitRequest $request, Unit $unit)
    {
        //
        $validated = $request->validated();

        $unit->update($validated);

        return redirect()->route('units.index')->with('success', 'تم تحديث الوحدة بنجاح');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Unit $unit)
    {
        //
        if($unit->contracts()->whereIn('status', ['active', 'pending'])->exists()) {
            return redirect()->route('units.index')
                    ->with('error', 'لا يمكن حذف الوحدة لوجود عقد نشط مرتبط بها.');
        }
        $unit->delete();

        return redirect()->route('units.index')->with('success', 'تم حذف الوحدة بنجاح');
    }
}
