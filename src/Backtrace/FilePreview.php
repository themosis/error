<?php

declare(strict_types=1);

namespace Themosis\Components\Error\Backtrace;

final class FilePreview
{
    public function __construct(
        private File $file,
    ) {
    }

    public function all_lines(): array
    {
        $lines = [];
        $file_resource = fopen($this->file->path(), 'r');      

        while ( !feof($file_resource) ) {
            $line = fgets($file_resource);

            if ( false !== $line ) {
                $lines[] = htmlentities($line);
            }
        }

        fclose($file_resource);

        return $lines;
    }

    public function get_lines( int $range = 10 ): array
    {
        $all_lines = $this->all_lines();

        $start = ($this->file->line() - $range) < 1
            ? 1
            : $this->file->line() - $range;

        $total = count($all_lines);
        $length = ($this->file->line() + $range) > $total
            ? (($total - $this->file->line()) < $this->file->line() ? $total : $total - $this->file->line())
            : ((1 === $start) ? $this->file->line() + ($this->file->line() - $start) : $range * 2);

        return array_slice(
            array: $all_lines,
            offset: $start - 1,
            length: $length,
            preserve_keys: true,
        );
    }

    public function is_current_line(int $line): bool
    {
        return $line === $this->file->line();
    }
}
