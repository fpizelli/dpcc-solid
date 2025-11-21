<?php

declare(strict_types=1);

namespace App\Domain\Entities;

use DateTime;

abstract class Vehicle
{
    protected string $plate;
    protected DateTime $entryTime;

    public function __construct(string $plate, DateTime $entryTime)
    {
        $this->plate = $plate;
        $this->entryTime = $entryTime;
    }

    public function getPlate(): string
    {
        return $this->plate;
    }

    public function getEntryTime(): DateTime
    {
        return $this->entryTime;
    }

    abstract public function getType(): string;
}
