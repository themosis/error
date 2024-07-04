<?php

declare(strict_types=1);

namespace Themosis\Components\Error\Backtrace;

final class Backtrace {
	/**
	 * @var array<int, Frame>
	 */
	private array $frames = [];

	public function __construct( array $frames ) {
		$this->frames = array_map( $this->add_frame( ... ), $frames );
	}

	private function add_frame( array $frame ): Frame {
		return new Frame( $frame );
	}
}

// BacktraceCapture;
// Backtrace->capture();
// Backtracer;
