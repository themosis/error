<?php

$foo = 'This is a text sample.';

$bar = 42;

array_map(static function ($num) {
    return $num;
}, [1,2,3]);

