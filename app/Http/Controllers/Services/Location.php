<?php

namespace App\Http\Controllers\Services;

use App\Enums\ModelsEnum;
use App\Http\Requests\ChangeLocationRequest;

class Location
{
    public function changeLocation(ModelsEnum $model, ChangeLocationRequest $request)
    {
        $entity = $model->value::where('mobile', $request->input('mobile'))->first();

        if (! $entity) {
            throw new \Exception('Mobile is not registered');
        }

        $entity->update(['latitude' => $request->latitude, 'longitude' => $request->longitude]);
    }
}
