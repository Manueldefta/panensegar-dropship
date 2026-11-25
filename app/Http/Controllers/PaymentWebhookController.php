<?php

namespace App\Http\Controllers;

use App\Services\MidtransService;
use Illuminate\Http\Request;

class PaymentWebhookController extends Controller
{
    public function handle(Request $request, MidtransService $midtransService)
    {
        $result = $midtransService->handleWebhook($request->all());

        return response()->json(['message' => $result['message']], $result['code']);
    }
}