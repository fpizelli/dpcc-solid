<?php

namespace App\Domain\Entity;

class Vehicle
{
    private ?int $id;
    private string $plate;
    private string $type;
    private string $entryAt;
    private ?string $exitAt;
    private ?float $price;

    public function __construct(
        ?int $id,
        string $plate,
        string $type,
        string $entryAt,
        ?string $exitAt = null,
        ?float $price = null
    ) {
        $this->id = $id;
        $this->plate = $plate;
        $this->type = $type;
        $this->entryAt = $entryAt;
        $this->exitAt = $exitAt;
        $this->price = $price;
    }

    public function id(): ?int { return $this->id; }
    public function plate(): string { return $this->plate; }
    public function type(): string { return $this->type; }
    public function entryAt(): string { return $this->entryAt; }
    public function exitAt(): ?string { return $this->exitAt; }
    public function price(): ?float { return $this->price; }

    public function setExit(string $exitAt, float $price): void
    {
        $this->exitAt = $exitAt;
        $this->price = $price;
    }
}
