<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Asset extends Model
{
    /** @use HasFactory<\Database\Factories\AssetFactory> */
    use HasFactory;


    protected $fillable = ['name', 'manager_id', 'assetable_type', 'assetable_id'];



    // manager/asset relationship
    public function manager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    // asset/agents relationship
    public function agents(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'asset_user', 'asset_id', 'agent_id')
        ->using(AssetUser::class)->withPivot('agency_status', 'assigned_at');
    }

    
    // polymorphic relationship: assets can take many forms->(prooperty , stock , car , etc)
    public function assetable(): MorphTo 
    {
        return $this->morphTo();
    }
    
}