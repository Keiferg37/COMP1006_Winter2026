<?php declare(strict_types=1);

//1. Set Up & Start 


//2. Code Commenting

// inline comment 
//3. Variables, Data Types, Concatenation, Conditional Statements & Echo

$firstName = "Keifer"; // string
$lastname = "Grainger"; // string
$age = 22; // integer
$isStudent = true; // boolean

echo "<p> Hello, my name is " . $firstName . " " . $lastname . "</p>";
if ($isStudent) {
    echo "<p> I am a student </p>";
} else {
    echo "<p>I am not a student.</p>";
}
//4. Loosely Typed Language Demo

$num1 = 1;
$num2 = 10;

function add(int $num1,int $num2) : int {
    return $num1 + $num2;
}

echo "<p>" . add($num1, $num2) . "</p>"; 

//5. Strict Types & Types Hints


//6. OOP with PHP
