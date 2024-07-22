<?php

declare(strict_types=1);

namespace Themosis\Components\Error;

use Generator;
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

    public function captured_exceptions(): Generator
    {
        foreach ($this->captured_exceptions as $exception) {
            yield $exception;
        }
    }

    public function release(): void
    {
        echo "Releasing captured exceptions...\n";

        while ($this->captured_exceptions()->valid()) {
            $exception = $this->captured_exceptions()->current();
            echo $exception->getMessage() . "\n";

            $this->captured_exceptions()->next();
        }
    }
}
