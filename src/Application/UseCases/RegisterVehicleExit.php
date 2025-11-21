<?php

declare(strict_types=1);

namespace App\Application\UseCases;

use App\Domain\Repositories\ParkingRepositoryInterface;
use App\Domain\Services\PricingStrategyInterface;
use DateTime;

class RegisterVehicleExit
{
    private ParkingRepositoryInterface $repository;
    private array $pricingStrategies;

    public function __construct(ParkingRepositoryInterface $repository, array $pricingStrategies)
    {
        $this->repository = $repository;
        $this->pricingStrategies = $pricingStrategies;
    }

    public function execute(string $plate): ?float
    {
        $record = $this->repository->findOpenRecordByPlate($plate);

        if ($record === null) {
            return null;
        }

        $entryTime = new DateTime((string)$record['entry_time']);
        $exitTime = new DateTime();

        $vehicleType = (string)$record['vehicle_type'];
        if (!isset($this->pricingStrategies[$vehicleType])) {
            return null;
        }

        $strategy = $this->pricingStrategies[$vehicleType];

        $price = $strategy->calculatePrice($entryTime, $exitTime);

        $this->repository->registerExit((int)$record['id'], $exitTime, $price);

        return $price;
    }
}
