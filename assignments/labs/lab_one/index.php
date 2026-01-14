<?php 
declare(strict_types=1);

require "header.php"; 
require "Car.php";   // include the class file

$car = new Car("Toyota", "Corolla", 2020);

echo "<p> Follow the instructions outlined in instructions.txt to complete this lab. Good luck & have fun!😀 </p>";
echo "<p><strong>Car Details:</strong> " . $car->getDetails() . "</p>";

require "footer.php";