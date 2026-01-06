<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AssetController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $assets = Asset::query()
            ->where('manager_id', Auth::id())
            ->with('property:id,asset_id,name,city,neighborhood')
            ->withCount('users')
            ->latest()
            ->get();

        return view('assets.index', compact('assets'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        return view('properties.index');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        return view('properties.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Asset $asset)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Asset $asset)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Asset $asset)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Asset $asset)
    {
        //
    }
}
