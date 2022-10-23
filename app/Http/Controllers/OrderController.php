<?php

namespace App\Http\Controllers;

use App\Actions\SaveOrderAction;
use App\DataTransferObjects\OrderData;
use App\Http\Requests\OrderPostRequest;

class OrderController extends Controller
{
    public function __invoke(OrderPostRequest $request)
    {
        SaveOrderAction::execute(OrderData::fromRequest($request));

        return response()->json(['success' => true]);
    }
}
