<?php

// SPDX-FileCopyrightText: 2024 Julien Lambé <julien@themosis.com>
//
// SPDX-License-Identifier: GPL-3.0-or-later

declare(strict_types=1);

$foo = 'This is a text sample.';

$bar = 42;

array_map(
    static function ($num) {
        return $num;
    },
    [ 1,2,3 ]
);
