<?php

try {
    include __DIR__ . '/another-nested-error.php';
} catch (RuntimeException $e) {
    throw new InformativeException(
        message: "Something went wrong here too!",
        previous: $e,
    );
}
