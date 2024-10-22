<?php

namespace App\Http\Controllers\Services;

use App\Enums\ModelsEnum;
use App\Http\Requests\ChangeLocationRequest;

class Location
{
    public function changeLocation(ModelsEnum $model, ChangeLocationRequest $request)
    {
        $entity = $model->value::where('mobile', $request->input('mobile'))->first();
    }
}
