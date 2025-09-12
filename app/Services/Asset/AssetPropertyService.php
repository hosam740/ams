<?php

namespace App\Services\Asset;

use App\Models\Asset;
use App\Models\assets\Property;
use Illuminate\Support\Facades\DB;

class AssetPropertyService{

    public function createPropertyWithAsset(array $property_data, array $asset_data): array{

        return DB::transaction(function() use($property_data, $asset_data){

            $property = Property::create($property_data);

            $asset = new Asset([
                'name' => $asset_data['name'],
                'manager_id' => $asset_data['manager_id']
            ]);

            $asset->assetable()->associate($property);

            $asset->save();

            return [$property, $asset];
        });
    }

    public function updatePropertyWithAsset(Property $property, array $property_data, array $asset_data): bool{

        return DB::transaction(function() use($property, $property_data, $asset_data){

            $property->update($property_data);
            $property->asset()->update(['name' => $asset_data['name']]);

            return true;
        });
    }

    public function destroyPropertyWithAsset(Property $property): bool {

        return DB::transaction(function() use ($property){
            $property->asset()->delete();
            $property->delete();
            return true;
        });
    }

}