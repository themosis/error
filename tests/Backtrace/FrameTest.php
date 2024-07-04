<?php

declare(strict_types=1);

namespace Themosis\Components\Error\Tests\Backtrace;

use PHPUnit\Framework\Attributes\Test;
use Themosis\Components\Error\Backtrace\Frame;
use Themosis\Components\Error\Tests\TestCase;

final class FrameTest extends TestCase {
	#[Test]
	public function it_can_generate_a_backtrace_frame_for_class_with_static_method(): void {
		$data = FramesProvider::class_with_static_method();

		$frame = new Frame( $data );

		$this->assertSame(
			'/disk/web/project/app/Something.php:22 App\Something::create()',
			(string) $frame,
		);
	}

	#[Test]
	public function it_can_generate_a_backtrace_frame_for_file_with_include(): void {
		$data = FramesProvider::file_with_include();

		$frame = new Frame( $data );

		$this->assertSame(
			'/disk/web/project/app/config.php:11 include()',
			(string) $frame,
		);
	}
}
