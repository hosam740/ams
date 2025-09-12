<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class AssetUser extends Pivot
{
    //many-to-many relationship between users and assets
}
