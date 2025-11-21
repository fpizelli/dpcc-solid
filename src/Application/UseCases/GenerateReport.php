<?php

declare(strict_types=1);

namespace App\Application\UseCases;

use App\Domain\Repositories\ParkingRepositoryInterface;

class GenerateReport
{
    private ParkingRepositoryInterface $repository;

    public function __construct(ParkingRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function execute(): array
    {
        return $this->repository->getReport();
    }
}
