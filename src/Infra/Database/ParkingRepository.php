<?php

declare(strict_types=1);

namespace App\Infra\Database;

use App\Domain\Repositories\ParkingRepositoryInterface;
use DateTime;
use PDO;

class ParkingRepository implements ParkingRepositoryInterface
{
    private PDO $pdo;

    public function __construct(SQLiteConnection $connection)
    {
        $this->pdo = $connection->getConnection();
    }

    public function registerEntry(
        string $plate,
        string $vehicleType,
        DateTime $entryTime
    ): void {
        $stmt = $this->pdo->prepare(
            'INSERT INTO parking_records (plate, vehicle_type, entry_time)
             VALUES (:plate, :vehicle_type, :entry_time)'
        );

        $stmt->execute([
            ":plate" => $plate,
            ":vehicle_type" => $vehicleType,
            ":entry_time" => $entryTime->format("Y-m-d H:i:s"),
        ]);
    }

    public function registerExit(
        int $id,
        DateTime $exitTime,
        float $price
    ): void {
        $stmt = $this->pdo->prepare(
            'UPDATE parking_records
             SET exit_time = :exit_time, price = :price
             WHERE id = :id'
        );

        $stmt->execute([
            ":exit_time" => $exitTime->format("Y-m-d H:i:s"),
            ":price" => $price,
            ":id" => $id,
        ]);
    }

    public function findOpenRecordByPlate(string $plate): ?array
    {
        $stmt = $this->pdo->prepare(
            'SELECT id, plate, vehicle_type, entry_time, exit_time, price
             FROM parking_records
             WHERE plate = :plate AND exit_time IS NULL
             ORDER BY entry_time DESC
             LIMIT 1'
        );

        $stmt->execute([":plate" => $plate]);

        $result = $stmt->fetch();

        return $result !== false ? $result : null;
    }

    public function getReport(): array
    {
        $stmt = $this->pdo->query(
            'SELECT vehicle_type,
                    COUNT(*) AS total,
                    COALESCE(SUM(price), 0) AS revenue
             FROM parking_records
             WHERE exit_time IS NOT NULL
             GROUP BY vehicle_type
             ORDER BY vehicle_type'
        );

        return $stmt->fetchAll();
    }
}
