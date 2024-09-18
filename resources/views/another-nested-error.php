<?php

throw new Exception('There was an error when calling the checkout client.', previous: new RuntimeException('Oops!'));

function odd($var)
{
    // retourne si l'entier en entrée est impair
    return $var & 1;
}

function even($var)
{
    // retourne si l'entier en entrée est pair
    return !($var & 1);
}

$array1 = ['a' => 1, 'b' => 2, 'c' => 3, 'd' => 4, 'e' => 5];
$array2 = [6, 7, 8, 9, 10, 11, 12];

echo "Impair :\n";
print_r(array_filter($array1, "odd"));
echo "Pair :\n";
print_r(array_filter($array2, "even"));

