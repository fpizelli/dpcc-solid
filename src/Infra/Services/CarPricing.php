<?php

declare(strict_types=1);

namespace App\Infra\Services;

use App\Domain\Services\PricingStrategyInterface;
use DateTime;

class CarPricing implements PricingStrategyInterface
{
    private float $rate = 5.0;

    public function calculatePrice(
        DateTime $entryTime,
        DateTime $exitTime
    ): float {
        $seconds = $exitTime->getTimestamp() - $entryTime->getTimestamp();
        $hours = (int) ceil($seconds / 3600);

        if ($hours < 1) {
            $hours = 1;
        }

        return $hours * $this->rate;
    }
}
