<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;


class Tenants extends Model
{
    /** @use HasFactory<\Database\Factories\TenantsFactory> */
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['first_name', 'last_name', 'national_id', 'phone_number', 'nationality'];



    // tenant/contract relationship
    public function contracts(): HasMany
    {
         return $this->hasMany(Contract::class, 'tenant_id');
    }
}
