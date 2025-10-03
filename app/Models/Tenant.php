<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;


class Tenant extends Model
{
    /** @use HasFactory<\Database\Factories\TenantFactory> */
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['first_name', 'last_name', 'national_id', 'phone_number', 'nationality'];



    // tenant/contract relationship
    public function contracts(): HasMany
    {
         return $this->hasMany(Contract::class, 'tenant_id');
    }
}
