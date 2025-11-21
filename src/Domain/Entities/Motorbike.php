<?php

declare(strict_types=1);

namespace App\Domain\Entities;

class Motorbike extends Vehicle
{
    public function getType(): string
    {
        return 'bike';
    }
}
