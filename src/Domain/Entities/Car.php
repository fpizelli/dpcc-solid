<?php

declare(strict_types=1);

namespace App\Domain\Entities;

class Car extends Vehicle
{
    public function getType(): string
    {
        return 'car';
    }
}
