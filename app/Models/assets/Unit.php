<?php

namespace App\Models\assets;

use App\Models\Contract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Unit extends Model
{
    /** @use HasFactory<\Database\Factories\Assets\UnitFactory> */
    use HasFactory;

    protected $fillable = ['name', 'type', 'description', 'area', 'status', 'property_id'];

    protected static $status_values = ['available', 'sold', 'rented', 'under_maintenance'];

    protected static $type_values = ['villa', 'apartment', 'office', 'warehouse', 'store'];



    // validating status input
    public function setStatusAttribute($value){

        if(!in_array($value, self::$status_values)){

            throw new \InvalidArgumentException("Invalid status value in unit model.");
        }

        $this->attributes['status'] = $value;
    }

    // validating type input
    public function setTypeAttribute($value){

        if(!in_array($value, self::$type_values)){

            throw new \InvalidArgumentException("Invalid type value in unit model.");
        }

        $this->attributes['type'] = $value;
    }



    public static function getStatusValues(): array{

        return self::$status_values;
    }

    public static function getTypeValues(): array{

        return self::$type_values;
    }



    // property/unit relationship -> one unit belonges to a property
    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class, 'property_id');
    }

    // contract/unit relationship - one unit can have multiple contracts over time
    public function contracts(): HasMany
    {
        return $this->hasMany(Contract::class, 'unit_id');
    }

}
