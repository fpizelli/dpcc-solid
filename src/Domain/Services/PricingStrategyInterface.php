<?php

declare(strict_types=1);

namespace App\Domain\Services;

use DateTime;

interface PricingStrategyInterface
{
    public function calculatePrice(DateTime $entryTime, DateTime $exitTime): float;
}
