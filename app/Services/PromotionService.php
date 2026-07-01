<?php

namespace App\Services;

use App\Models\Client;
use App\Models\Promotion;
use Illuminate\Support\Collection;

class PromotionService
{
    public function findBestPromotion(
        float $subtotal,
        array $cart,
        ?int $clientId = null,
        ?int $productId = null
    ): ?Promotion
    {
        $query = Promotion::active()
            ->where('is_active', true)
            ->where('start_date', '<=', today())
            ->where('end_date', '>=', today());

        if ($query->get()->isEmpty()) {
            return null;
        }

        $cartProductIds = collect($cart)->pluck('product_id')->toArray();
        $totalQuantity = collect($cart)->sum('quantity');

        $promotions = $query->get()->filter(function ($promotion) use ($subtotal, $totalQuantity, $clientId, $cartProductIds) {
            if ($promotion->min_amount > 0 && $subtotal < (float) $promotion->min_amount) {
                return false;
            }
            if ($promotion->min_quantity > 0 && $totalQuantity < $promotion->min_quantity) {
                return false;
            }
            if ($promotion->usage_limit && $promotion->used_count >= $promotion->usage_limit) {
                return false;
            }
            if ($promotion->applies_to === 'products') {
                $promotionProductIds = $promotion->products->pluck('id')->toArray();
                if (empty(array_intersect($cartProductIds, $promotionProductIds))) {
                    return false;
                }
            }
            if ($promotion->applies_to === 'groups' && $clientId) {
                $client = Client::with('customerGroup')->find($clientId);
                if (!$client || !$client->customerGroup) {
                    return false;
                }
                $groupIds = $promotion->customerGroups->pluck('id')->toArray();
                if (!in_array($client->customer_group_id, $groupIds)) {
                    return false;
                }
            }
            return true;
        });

        $best = null;
        $bestDiscount = 0;

        foreach ($promotions as $promotion) {
            $discount = $promotion->calculateDiscount($subtotal);
            if ($discount > $bestDiscount) {
                $bestDiscount = $discount;
                $best = $promotion;
            }
        }

        return $best;
    }
}
