<?php

declare(strict_types=1);

namespace Themosis\Components\Error;

use SplDoublyLinkedList;
use Throwable;

final class ShutdownErrorHandler
{
    public function __construct(
        private SplDoublyLinkedList $captured_exceptions,
    ) {
        register_shutdown_function( $this->release(...) );
    }

    public function capture(Throwable $exception): self
    {
        $this
            ->captured_exceptions
            ->push($exception);

        return $this;
    }

    public function release(): void
    {
        echo "Releasing captured exceptions...\n";

        foreach ($this->captured_exceptions as $exception) {
            echo $exception->getMessage() . "\n";
        }
    }
}
