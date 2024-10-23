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

    private int $totalRows;

    /**
     * @var array<int, FilePreviewLine>
     */
    private array $lines = [];

    public function __construct(
        private File $file,
    ) {
        $this->resource = new SplFileObject($this->file->path());
        $this->totalRows = $this->getTotalRows();
    }

    private function getTotalRows(): int
    {
        $this->resource->seek(PHP_INT_MAX);

        return $this->resource->key();
    }

    /**
     * @return array<int, FilePreviewLine>
     */
    public function getLines(int $range = 10): array
    {
        if (! empty($this->lines)) {
            return $this->lines;
        }

        $adjustRange = function (int $initialRange) {
            $lineNumber = $this->file->line();
            $topRange = $initialRange;
            $bottomRange = $initialRange;

            if (( $lineNumber - $initialRange ) < 1) {
                $topRange = $lineNumber - 1;
            }

            if (( $lineNumber + $initialRange ) > $this->totalRows) {
                $bottomRange = $this->totalRows - $lineNumber;
            }

            return min($topRange, $bottomRange, $initialRange);
        };

        $range = $adjustRange($range);
        $startRow = $this->file->line() - $range;
        $endRow = $this->file->line() + $range;

        $currentRow = $startRow;

        while ($currentRow <= $endRow) {
            $this->resource->seek($currentRow - 1);

            $this->lines[] = new FilePreviewLine(
                content: (string) $this->resource->fgets(),
                number: $currentRow,
            );

            ++$currentRow;
        }

        return $this->lines;
    }

    public function rowNumberLength(): int
    {
        $rowNumbers = array_map(static fn (FilePreviewLine $line) => $line->number(), $this->getLines());
        $highestRowNumber = max($rowNumbers);

        return mb_strlen((string) $highestRowNumber);
    }

    public function isCurrentLine(int $line): bool
    {
        return $line === $this->file->line();
    }
}
