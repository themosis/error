<?php

declare(strict_types=1);

namespace Themosis\Components\Error\Backtrace;

use Stringable;
use Throwable;

final class Backtrace implements Stringable {
	/**
	 * @var array<int, Frame>
	 */
	private array $frames = [];

	public function __construct(
		private FrameIdentifiers $frame_identifiers,
	) {}

	public static function debug( FrameIdentifiers $frame_identifiers = null ): self {
		$self = new self(
			frame_identifiers: $frame_identifiers ?? new InMemoryFrameIdentifiers(),
		);

		$debug_backtrace_without_first_frame = ( static function ( array $frames ): array {
			return array_slice( $frames, 1 );
		} )( debug_backtrace() );

		return $self->capture(
			frames: $debug_backtrace_without_first_frame,
		);
	}

	public function capture( array $frames ): self {
		$this->frames = array_map( $this->make_frame( ... ), $frames );

		return $this;
	}

	public function capture_exception( Throwable $exception ): self {
		return $this->capture(
			frames: $exception->getTrace(),
		);
	}

	public function frames(): array {
		return $this->frames;
	}

	public function filter( callable $filter_callback ): self {
		$filtered_backtrace = new self(
			frame_identifiers: $this->frame_identifiers,
		);

		$filtered_frames = array_map( fn( Frame $frame ) => $frame->as_array(), array_filter( $this->frames, $filter_callback ) );
		$filtered_backtrace->capture( $filtered_frames );

		return $filtered_backtrace;
	}

	private function make_frame( array $frame_args ): Frame {
		$frame = new Frame( $frame_args );

		$applicable_identifiers = array_filter(
			$this->frame_identifiers->all(),
			function ( FrameIdentifier $frame_identifier ) use ( $frame ) {
				return $frame_identifier->identify( $frame );
			}
		);

		$frame->add_tag( ...array_map( fn( FrameIdentifier $frame_identifier ) => $frame_identifier->tag(), $applicable_identifiers ) );

		return $frame;
	}

	public function __toString(): string {
		return implode(
			PHP_EOL,
			array_map(
				function ( Frame $frame, int $index ) {
					return sprintf( '[%d] %s', $index, (string) $frame );
				},
				$this->frames,
				array_keys( $this->frames )
			)
		);
	}

	public function __debugInfo() {
		return array_map(
			function ( Frame $frame ) {
				return (string) $frame;
			},
			$this->frames,
		);
	}
}
