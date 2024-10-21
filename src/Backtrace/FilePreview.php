<?php

// SPDX-FileCopyrightText: 2024 Julien Lambé <julien@themosis.com>
//
// SPDX-License-Identifier: GPL-3.0-or-later

declare(strict_types=1);

namespace Themosis\Components\Error\Backtrace;

use SplFileObject;

final class FilePreview
{
    private SplFileObject $resource;

    private int $total_rows;

    /**
     * @var array<int, FilePreviewLine>
     */
    private array $lines = [];

    public function __construct(
        private File $file,
    ) {
        $this->resource   = new SplFileObject($this->file->path());
        $this->total_rows = $this->get_total_rows();
    }

    private function get_total_rows(): int
    {
        $this->resource->seek(PHP_INT_MAX);

        return $this->resource->key();
    }

    /**
     * @return array<int, FilePreviewLine>
     */
    public function get_lines(int $range = 10): array
    {
        if (! empty($this->lines)) {
            return $this->lines;
        }

        $adjust_range = function (int $initial_range) {
            $line_number  = $this->file->line();
            $top_range    = $initial_range;
            $bottom_range = $initial_range;

            if (( $line_number - $initial_range ) < 1) {
                $top_range = $line_number - 1;
            }

            if (( $line_number + $initial_range ) > $this->total_rows) {
                $bottom_range = $this->total_rows - $line_number;
            }

            return min($top_range, $bottom_range, $initial_range);
        };

        $range     = $adjust_range($range);
        $start_row = $this->file->line() - $range;
        $end_row   = $this->file->line() + $range;

        $current_row = $start_row;

        while ($current_row <= $end_row) {
            $this->resource->seek($current_row - 1);

            $this->lines[] = new FilePreviewLine(
                content: (string) $this->resource->fgets(),
                number: $current_row,
            );

            ++$current_row;
        }

        return $this->lines;
    }

    public function row_number_length(): int
    {
        $row_numbers        = array_map(static fn (FilePreviewLine $line) => $line->number(), $this->get_lines());
        $highest_row_number = max($row_numbers);

        return mb_strlen((string) $highest_row_number);
    }

    public function is_current_line(int $line): bool
    {
        return $line === $this->file->line();
    }
}
