<?php declare(strict_types=1);

// Car class definition
class Car {
    // Properties
    private string $make;
    private string $model;
    private int $year;

    // Constructor
    public function __construct(string $make, string $model, int $year) {
        $this->make = $make;
        $this->model = $model;
        $this->year = $year;
    }

    // Method to get car details
    public function getDetails(): string {
        return "Car Details: " . $this->year . " " . $this->make . " " . $this->model;
    }

}