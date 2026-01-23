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


echo "<p> Hello there, my name is " . $firstname . " " . $lastName . "</p>";


if ($is_student === true) {
    echo "<p> I am your teacher! </p>";
} else {
    echo "<p> Uh oh, teach didn't show! </p>";
}

//3. PHP is loosely typed 

$num1 = 5;
$num2 = "10";

echo "<p>" . add($num1, $num2) . "</p>";

//4. Strict Types & Types Hints
/* Add declare(strict_types=1) and type hints 
//strict types tell PHP not to automatically convert values when calling functions. Type hints tell PHP what to expect */

/*function add(int $num1, int $num2): int {
    return $num1 + $num2;
}
echo add($num1, $num2);
*/

//6. OOP with PHP
