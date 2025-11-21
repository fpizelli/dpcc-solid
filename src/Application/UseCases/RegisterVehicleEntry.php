<?php

declare(strict_types=1);

namespace App\Application\UseCases;

use App\Domain\Entities\Car;
use App\Domain\Entities\Motorbike;
use App\Domain\Entities\Truck;
use App\Domain\Repositories\ParkingRepositoryInterface;
use DateTime;

class RegisterVehicleEntry
{
    private ParkingRepositoryInterface $repository;

    public function __construct(ParkingRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function execute(string $plate, string $vehicleType): void
    {
        $entryTime = new DateTime();

        if ($vehicleType === 'car') {
            $vehicle = new Car($plate, $entryTime);
        } elseif ($vehicleType === 'bike') {
            $vehicle = new Motorbike($plate, $entryTime);
        } else {
            $vehicle = new Truck($plate, $entryTime);
        }

        $this->repository->registerEntry(
            $vehicle->getPlate(),
            $vehicle->getType(),
            $vehicle->getEntryTime()
        );
    }
}
