<?php

declare(strict_types=1);

namespace Themosis\Components\Error;

/**
 * Repository where a developer could register frame types
 * in order to identify/filter backtrace individual frames:
 *
 * -> Declare how to find php core frames
 * -> Declare how to find themosis component frames
 * -> Declare how to find WordPress frames
 * ...
 */
interface FrameTypes {
	public function add(): void;

	public function find(): void;
}
