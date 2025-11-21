<?php

declare(strict_types=1);

namespace App\Domain\Entities;

class Truck extends Vehicle
{
    public function getType(): string
    {
        return 'truck';
    }
}
