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
    private array $raw_frame;
    private FrameFunction $function;
    private File $file;
    private ?object $object;
    private array $args;

    /**
     * @var array<string, FrameTag>
     */
    private array $tags = [];

    public function __construct(array $frame)
    {
        $this->raw_frame = $frame;

        $this->file = new File(
            filepath: $frame['file'] ?? null,
            line: $frame['line'] ?? null,
        );

        $this->function = isset($frame['class'])
            ? new ClassFunction(
                class_name: $frame['class'],
                function_name: $frame['function'],
                type: $frame['type'],
            )
            : new PhpFunction(
                function_name: $frame['function'],
            );

        $this->object = $frame['object'] ?? null;
        $this->args   = $frame['args'] ?? [];
    }

    public function add_tag(FrameTag ...$tags): void
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
        return new ArrayIterator($this->raw_frame);
    }

    public function get_function(): FrameFunction
    {
        return $this->function;
    }

    public function get_file(): File
    {
        return $this->file;
    }

    public function get_object(): ?object
    {
        return $this->object;
    }

    public function get_args(): array
    {
        return $this->args;
    }

    public function as_array(): array
    {
        return $this->raw_frame;
    }

    public function __toString(): string
    {
        $elements = [
            $this->file,
            $this->function,
        ];

        return implode(' ', $elements);
    }
}
