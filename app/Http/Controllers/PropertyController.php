<?php

namespace App\Http\Controllers;

use App\Models\assets\Property;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\Asset\AssetPropertyService;

class PropertyController extends Controller
{
    

    private function storeRules()
    {
        return [
            'name'         => 'required|string|max:255',
            'country'      => 'required|string|max:100',
            'city'         => 'required|string|max:100',
            'neighborhood' => 'required|string|max:100',
            'url_location' => 'nullable|string',
            'area'         => 'required|numeric|min:1',
        ];
    }

    private function updateRules()
    {
        return [
            'name'         => 'sometimes|string|max:255',
            'country'      => 'sometimes|string|max:100',
            'city'         => 'sometimes|string|max:100',
            'neighborhood' => 'sometimes|string|max:100',
            'url_location' => 'sometimes|nullable|string',
            'area'         => 'sometimes|numeric|min:0',
        ];
    }

    private function split(array $data): array{

        $property_data = collect($data)->except('name')->toArray();

        $asset_data = collect($data)->only('name')->toArray();

        return [$property_data, $asset_data];
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $properties = Property::query()
                ->whereHas('asset', function ($q) {
                    $q->where('manager_id', Auth::id());})
                    ->with(['asset'])
                    ->withCount([
                        'units',
                        'units as units_available_count' => function ($query) {
                            $query->where('status', 'available');
                        },
                    ])
                    ->get();



        return view('properties.index', compact('properties'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        return view('properties.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, AssetPropertyService $assetPropertyService)
    {
        //
        $validated = $request->validate($this->storeRules());

        // this method splits the $validated data into $property_data and $asset_data
        [$property_data, $asset_data] =$this->split($validated);
        $asset_data['manager_id'] = Auth::id();

        // every property belongs to an asset so this method creates the property with its asset
        [$property, $asset] = $assetPropertyService->createPropertyWithAsset($property_data, $asset_data);

        return redirect()->route('properties.index')->with('success', 'تم إضافة العقار بنجاح');

    }

    /**
     * Display the specified resource.
     */
    public function show(Property $property)
    {
        $property->load([
            'asset',
            'units' => function ($query) {
                $query->select('id', 'property_id', 'name', 'type', 'status', 'area')
                      ->orderBy('name');
            },
        ]);

        return view('properties.show', compact('property'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Property $property)
    {
        //
        return view('properties.edit', compact('property'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Property $property, AssetPropertyService $assetPropertyService)
    {
        //
        $validated = $request->validate($this->updateRules());

        // this method splits the $validated data into $property_data and $asset_data
        [$property_data, $asset_data] =$this->split($validated);

        // every property belongs to an asset so this method update the property with its asset
        $assetPropertyService->updatePropertyWithAsset($property, $property_data, $asset_data);

        return redirect()->route('properties.index')->with('success', 'تم تحديث العقار بنجاح');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Property $property, AssetPropertyService $assetPropertyService)
    {
        //
        $assetPropertyService->destroyPropertyWithAsset($property);

        return redirect()->route('properties.index')->with('success', 'تم حذف العقار بنجاح');
    }
}
