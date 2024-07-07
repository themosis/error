<?php

declare(strict_types=1);

namespace Themosis\Components\Error\Backtrace;

final class Backtrace {
	/**
	 * @var array<int, Frame>
	 */
	private array $frames = [];

	public function __construct(
		private FrameTags $tags,
	) {
	}

	public function capture( array $frames ): void {
		$this->frames = array_map( $this->add_frame( ... ), $frames );
	}

	public function frames(): array {
		return $this->frames;
	}

	public function filter( callable $filter_callback ): self {
		$filtered_backtrace = new self(
			tags: $this->tags,
		);

		$filtered_frames = array_map( fn ( Frame $frame ) => $frame->as_array(), array_filter( $this->frames, $filter_callback ) );
		$filtered_backtrace->capture( $filtered_frames );

		return $filtered_backtrace;
	}

	private function add_frame( array $frame_args ): Frame {
		$frame = new Frame( $frame_args );

		$this->tags->find( $frame );

		return $frame;
	}
}
