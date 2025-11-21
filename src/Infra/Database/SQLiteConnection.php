<?php

declare(strict_types=1);

namespace App\Infra\Database;

use PDO;
use PDOException;

class SQLiteConnection
{
    private PDO $connection;

    public function __construct(string $databasePath)
    {
        $this->connection = new PDO("sqlite:" . $databasePath);
        $this->connection->setAttribute(
            PDO::ATTR_ERRMODE,
            PDO::ERRMODE_EXCEPTION
        );
        $this->connection->setAttribute(
            PDO::ATTR_DEFAULT_FETCH_MODE,
            PDO::FETCH_ASSOC
        );

        $this->initializeSchema();
    }

    public function getConnection(): PDO
    {
        return $this->connection;
    }

    private function initializeSchema(): void
    {
        $sql = <<<SQL
CREATE TABLE IF NOT EXISTS parking_records (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    plate TEXT NOT NULL,
    vehicle_type TEXT NOT NULL,
    entry_time TEXT NOT NULL,
    exit_time TEXT DEFAULT NULL,
    price REAL DEFAULT NULL
);
SQL;

        try {
            $this->connection->exec($sql);
        } catch (PDOException $e) {
            throw new PDOException(
                "Erro ao inicializar o schema SQLite: " . $e->getMessage(),
                (int) $e->getCode()
            );
        }
    }
}
