<?php

// SPDX-FileCopyrightText: 2024 Julien Lambé <julien@themosis.com>
//
// SPDX-License-Identifier: GPL-3.0-or-later

declare(strict_types=1);

namespace Themosis\Components\Error\Backtrace;

use ArrayIterator;
use IteratorAggregate;
use Stringable;
use Traversable;

final class Frame implements Stringable, IteratorAggregate
{
    private array $rawFrame;
    private FrameFunction $function;
    private ?File $file = null;
    private ?object $object = null;
    private array $args;

    /**
     * @var array<string, FrameTag>
     */
    private array $tags = [];

    public function __construct(array $frame)
    {
        $this->rawFrame = $frame;

        if (isset($frame['file']) && isset($frame['line'])) {
            $this->file = new File(
                filepath: $frame['file'],
                line: $frame['line'],
            );
        }

        $this->function = isset($frame['class'])
            ? new ClassFunction(
                className: $frame['class'],
                functionName: $frame['function'],
                type: $frame['type'],
            )
            : new PhpFunction(
                functionName: $frame['function'],
            );

        $this->object = $frame['object'] ?? null;
        $this->args = $frame['args'] ?? [];
    }

    public function addTag(FrameTag ...$tags): void
    {
        foreach ($tags as $tag) {
            $this->tags[ $tag->slug() ] = $tag;
        }
    }

    public function tags(): array
    {
        return $this->tags;
    }

    public function is(FrameTag $tag): bool
    {
        return isset($this->tags[ $tag->slug() ]) && $tag->equals($this->tags[ $tag->slug() ]);
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->rawFrame);
    }

    public function getFunction(): FrameFunction
    {
        return $this->function;
    }

    public function getFile(): ?File
    {
        return $this->file;
    }

    public function getObject(): ?object
    {
        return $this->object;
    }

    public function getArgs(): array
    {
        return $this->args;
    }

    public function toArray(): array
    {
        return $this->rawFrame;
    }

    public function __toString(): string
    {
        $elements = [
            $this->file,
            $this->function,
        ];

        return implode(' ', array_filter($elements));
    }
}
