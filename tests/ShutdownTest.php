<?php

declare(strict_types=1);

namespace Themosis\Components\Error\Tests;

use Exception;
use LogicException;
use PHPUnit\Framework\Attributes\Test;
use RuntimeException;
use SplQueue;
use Themosis\Components\Error\ShutdownErrorHandler;

final class ShutdownTest extends TestCase
{
    #[Test]
    public function it_can_shutdown(): void
    {
        $queue = new SplQueue();
        $queue->setIteratorMode(SplQueue::IT_MODE_DELETE);

        $error_handler = new ShutdownErrorHandler($queue);

        $error_handler->capture(new RuntimeException('Foo is not allowed.'));
        $error_handler->capture(new LogicException('Bar is not enabled.'));

        $queue = new SplQueue();
        $queue->setIteratorMode(SplQueue::IT_MODE_DELETE);

        $other_handler = new ShutdownErrorHandler($queue);

        $other_handler->capture(new RuntimeException('Baz is not connected.'));
        $other_handler->capture(new RuntimeException('Something went wrong...'));

        try {
            throw new RuntimeException('Client is not available.');
        } catch (Exception $exception) {
            $other_handler->capture($exception);
        }

        echo "This should be processed before captured exceptions...\n";
    }
}
