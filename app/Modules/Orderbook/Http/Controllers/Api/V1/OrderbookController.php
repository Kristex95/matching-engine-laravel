<?php

declare(strict_types=1);

namespace App\Modules\Orderbook\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Redis;

class OrderbookController extends Controller
{
    public function __construct() {}

    public function show(string $symbol): JsonResponse
    {
        $key = 'orderbook:' . strtoupper($symbol);

        $raw = Redis::get($key);

        if (!$raw) {
            return response()->json([
                'message' => 'Orderbook not found'
            ], Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($raw, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return response()->json([
                'message' => 'Corrupted orderbook data'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json($data);
    }
}
