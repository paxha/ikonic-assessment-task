<?php

namespace App\Services;

use App\Models\Affiliate;
use App\Models\Merchant;
use App\Models\Order;
use App\Models\User;

class OrderService
{
    public function __construct(
        protected AffiliateService $affiliateService
    ) {}

    /**
     * Process an order and log any commissions.
     * This should create a new affiliate if the customer_email is not already associated with one.
     * This method should also ignore duplicates based on order_id.
     *
     * @param  array{order_id: string, subtotal_price: float, merchant_domain: string, discount_code: string, customer_email: string, customer_name: string} $data
     * @return void
     */
    public function processOrder(array $data)
    {
        if (Order::whereExternalOrderId($data['order_id'])->exists()) {
            return;
        }

        $merchant = Merchant::whereDomain($data['merchant_domain'])->first();

        $this->affiliateService->register(merchant: $merchant, email: $data['customer_email'], name: $data['customer_name'], commissionRate: 0.1);

        Order::create([
            'merchant_id' => $merchant->id,
            'affiliate_id' => $merchant->affiliate->id,
            'subtotal' => $data['subtotal_price'],
            'commission_owed' => $data['subtotal_price'] * (float) $merchant->affiliate->commission_rate,
            'external_order_id' => $data['order_id'],
        ]);
    }
}
