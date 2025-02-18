<?php

// SPDX-FileCopyrightText: 2024 Julien Lambé <julien@themosis.com>
//
// SPDX-License-Identifier: GPL-3.0-or-later

declare(strict_types=1);

namespace Themosis\Components\Error\Backtrace;

use Stringable;
use Throwable;

final class Backtrace implements Stringable
{
    /**
     * @var array<int, Frame>
     */
    private array $frames = [];

    public function __construct(
        private FrameIdentifiers $frameIdentifiers,
    ) {
    }

    public static function debug(FrameIdentifiers $frameIdentifiers = null): self
    {
        $self = new self(
            frameIdentifiers: $frameIdentifiers ?? new InMemoryFrameIdentifiers(),
        );

        $debugBacktraceWithoutFirstFrame = ( static function (array $frames): array {
            return array_slice($frames, 1);
        } )(debug_backtrace());

        return $self->capture(
            frames: $debugBacktraceWithoutFirstFrame,
        );
    }

    /**
     * @param array<int, array<string, mixed>> $frames
     */
    public function capture(array $frames): self
    {
        $this->frames = array_map($this->makeFrame(...), $frames);

        return $this;
    }

    public function captureException(Throwable $exception): self
    {
        return $this->capture(
            frames: $exception->getTrace(),
        );
    }

    /**
     * @return array<int, Frame>
     */
    public function frames(): array
    {
        return $this->frames;
    }

    public function filter(callable $filterCallback): self
    {
        $filteredBacktrace = new self(
            frameIdentifiers: $this->frameIdentifiers,
        );

        $filteredFrames = array_map(
            fn(Frame $frame) => $frame->toArray(),
            array_filter($this->frames, $filterCallback)
        );

        $filteredBacktrace->capture($filteredFrames);

        return $filteredBacktrace;
    }

    /**
     * @param array<string, mixed> $frameArgs
     */
    private function makeFrame(array $frameArgs): Frame
    {
        $frame = new Frame($frameArgs);

        $applicableIdentifiers = array_filter(
            $this->frameIdentifiers->all(),
            function (FrameIdentifier $frameIdentifier) use ($frame) {
                return $frameIdentifier->identify($frame);
            }
        );

        $frame->addTag(...array_map(
            fn(FrameIdentifier $frameIdentifier) => $frameIdentifier->tag(),
            $applicableIdentifiers
        ));

        return $frame;
    }

    public function __toString(): string
    {
        return implode(
            PHP_EOL,
            array_map(
                function (Frame $frame, int $index) {
                    return sprintf('[%d] %s', $index, (string) $frame);
                },
                $this->frames,
                array_keys($this->frames)
            )
        );
    }

    public function __debugInfo(): array
    {
        return array_map(
            function (Frame $frame) {
                return (string) $frame;
            },
            $this->frames,
        );
    }
}
