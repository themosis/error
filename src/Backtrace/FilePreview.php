<?php

declare(strict_types=1);

namespace Themosis\Components\Error\Backtrace;

use SplFileObject;

final class FilePreview {
	private SplFileObject $resource;

	private int $total_rows;

	private array $lines = [];

	public function __construct(
		private File $file,
	) {
		$this->resource   = new SplFileObject( $this->file->path() );
		$this->total_rows = $this->get_total_rows();
	}

	private function get_total_rows(): int {
		$this->resource->seek( PHP_INT_MAX );

		return $this->resource->key();
	}

	public function get_lines( int $range = 10 ): array {
		if ( ! empty( $this->lines ) ) {
			return $this->lines;
		}

		$calculate_range = function ( int $initial_range ) {
			$line_number = $this->file->line();

			if ( ( $line_number - $initial_range ) < 1 ) {
				return $line_number - 1;
			}

			if ( ( $line_number + $initial_range ) > $this->total_rows ) {
				return $this->total_rows - $line_number;
			}

			return $initial_range;
		};

		$range     = $calculate_range( $range );
		$start_row = $this->file->line() - $range;
		$end_row   = $this->file->line() + $range;

		$current_row = $start_row;

		while ( $current_row <= $end_row ) {
			$this->resource->seek( $current_row - 1 );

			$this->lines[ $current_row ] = htmlentities( $this->resource->fgets() );
			++$current_row;
		}

		return $this->lines;
	}

	public function row_number_length(): int {
		$row_numbers        = array_keys( $this->get_lines() );
		$highest_row_number = max( ...$row_numbers );

		return mb_strlen( (string) $highest_row_number );
	}

	public function is_current_line( int $line ): bool {
		return $line === $this->file->line();
	}
}
