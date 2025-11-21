<?php

declare(strict_types=1);

namespace App\Domain\Repositories;

use DateTime;

interface ParkingRepositoryInterface
{
    public function registerEntry(
        string $plate,
        string $vehicleType,
        DateTime $entryTime
    ): void;

    public function registerExit(
        int $id,
        DateTime $exitTime,
        float $price
    ): void;

    public function findOpenRecordByPlate(string $plate): ?array;

    public function getReport(): array;
}
