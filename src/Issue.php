<?php

namespace Themosis\Components\Error;

use Throwable;

final class Issue
{
    public function __construct(
        private Throwable $exception,
    ) {
    }
}
