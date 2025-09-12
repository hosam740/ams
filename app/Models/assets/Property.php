<?php

namespace App\Models\assets;

use App\Models\Asset;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class Property extends Model
{
    /** @use HasFactory<\Database\Factories\Assets\PropertyFactory> */
    use HasFactory;

    protected $fillable = ['country', 'city', 'neighborhood', 'url_location', 'area'];

    
    // asset/property relationship -> properties are form of asseets
    public function asset(): MorphOne
    {
        return $this->morphOne(Asset::class, 'assetable');
    }


    // property/unit relationship -> property has many units
    public function units(): HasMany
    {
        return $this->hasMany(Unit::class, 'property_id');
    }


}
