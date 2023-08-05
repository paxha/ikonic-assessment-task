<?php

namespace App\Http\Controllers;

use App\Models\Merchant;
use App\Services\MerchantService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class MerchantController extends Controller
{
    public function __construct(
        MerchantService $merchantService
    ) {}

    /**
     * Useful order statistics for the merchant API.
     *
     * @param Request $request Will include a from and to date
     * @return JsonResponse Should be in the form {count: total number of orders in range, commission_owed: amount of unpaid commissions for orders with an affiliate, revenue: sum order subtotals}
     */
    public function orderStats(Request $request): JsonResponse
    {
        $between = $request->user()->merchant->orders()->whereBetween('created_at', [$request->from, $request->to]);


        $noAffiliate = $request->user()->merchant->orders()->whereNull('affiliate_id')->get();

        return response()->json([
            'count' => $between->count(),
            'revenue' => $between->sum('subtotal'),
            'commissions_owed' => $between->sum('commission_owed') - $noAffiliate->sum('commission_owed'),
        ]);
    }
}
