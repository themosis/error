<?php

namespace Themosis\Components\Error\Tests;

use PHPUnit\Framework\Attributes\Test;
use Themosis\Components\Error\ErrorHandler;
use Themosis\Components\Error\InMemoryReporters;

final class ErrorHandlerTest extends TestCase
{
    #[Test]
    public function it_can_test(): void
    {
        $error_handler = new ErrorHandler(
            reporters: new InMemoryReporters(),
        );

        $error_handler->capture();
    }
}
