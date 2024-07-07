<?php

declare(strict_types=1);

namespace Themosis\Components\Error\Backtrace;

final class Backtrace {
	/**
	 * @var array<int, Frame>
	 */
	private array $frames = [];

	public function __construct(
		private FrameIdentifiers $frame_identifiers,
	) {
	}

	public function capture( array $frames ): self {
		$this->frames = array_map( $this->add_frame( ... ), $frames );

		return $this;
	}

	public function frames(): array {
		return $this->frames;
	}

	public function filter( callable $filter_callback ): self {
		$filtered_backtrace = new self(
			frame_identifiers: $this->frame_identifiers,
		);

		$filtered_frames = array_map( fn ( Frame $frame ) => $frame->as_array(), array_filter( $this->frames, $filter_callback ) );
		$filtered_backtrace->capture( $filtered_frames );

		return $filtered_backtrace;
	}

	private function add_frame( array $frame_args ): Frame {
		$frame = new Frame( $frame_args );

		$applicable_identifiers = array_filter(
			$this->frame_identifiers->all(),
			function ( FrameIdentifier $frame_identifier ) use ( $frame ) {
				return $frame_identifier->identify( $frame );
			}
		);

		$frame->add_tag( ...array_map( fn ( FrameIdentifier $frame_identifier ) => $frame_identifier->tag(), $applicable_identifiers ) );

		return $frame;
	}
}
