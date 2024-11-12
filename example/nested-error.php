<?php

// SPDX-FileCopyrightText: 2024 Julien Lambé <julien@themosis.com>
//
// SPDX-License-Identifier: GPL-3.0-or-later

try {
    include __DIR__ . '/another-nested-error.php';
} catch (RuntimeException $e) {
    throw new InformativeException(
        message: "Something went wrong here too!",
        previous: $e,
    );
}
