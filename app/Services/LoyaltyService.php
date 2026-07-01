<?php

namespace App\Services;

use App\Models\Client;
use App\Models\LoyaltyTransaction;

class LoyaltyService
{
    const POINTS_PER_CURRENCY = 10;

    const REDEEM_RATE = 100;

    public function earnPoints(Client $client, float $amountSpent, ?string $referenceType = null, ?int $referenceId = null, ?string $description = null): int
    {
        $points = (int) floor($amountSpent / self::POINTS_PER_CURRENCY);

        if ($points <= 0) {
            return 0;
        }

        $client->increment('points', $points);
        $client->increment('total_spent', $amountSpent);
        $client->update(['last_purchase_at' => now()]);

        LoyaltyTransaction::create([
            'client_id' => $client->id,
            'points' => $points,
            'type' => 'earn',
            'reference_type' => $referenceType,
            'reference_id' => $referenceId,
            'description' => $description ?? "Compra de " . currency($amountSpent),
        ]);

        return $points;
    }

    public function redeemPoints(Client $client, int $points, ?string $referenceType = null, ?int $referenceId = null, ?string $description = null): float
    {
        $points = min($points, $client->points);

        $discount = $points / self::REDEEM_RATE;

        $client->decrement('points', $points);

        LoyaltyTransaction::create([
            'client_id' => $client->id,
            'points' => -$points,
            'type' => 'redeem',
            'reference_type' => $referenceType,
            'reference_id' => $referenceId,
            'description' => $description ?? "Canje de " . number_format($points) . " puntos",
        ]);

        return $discount;
    }

    public function calculateDiscountValue(int $points): float
    {
        return $points / self::REDEEM_RATE;
    }

    public function canRedeem(Client $client, int $points): bool
    {
        return $client->points >= $points;
    }
}
