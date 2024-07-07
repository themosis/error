<?php

declare(strict_types=1);

namespace Themosis\Components\Error\Tests\Backtrace;

use PHPUnit\Framework\Attributes\Test;
use Themosis\Components\Error\Backtrace\Backtrace;
use Themosis\Components\Error\Backtrace\Frame;
use Themosis\Components\Error\Backtrace\FrameClassFunction;
use Themosis\Components\Error\Backtrace\FrameTag;
use Themosis\Components\Error\Backtrace\InMemoryFrameTags;
use Themosis\Components\Error\Tests\TestCase;

final class BacktraceTest extends TestCase {
	#[Test]
	public function it_can_create_a_backtrace_using_php_debug_backtace(): void {
		$tags = new InMemoryFrameTags();

		$tags->add(
			tag: new FrameTag(
				slug: 'error_component_tests',
				name: 'Test',
			)
		);

		$backtrace = new Backtrace(
			tags: $tags,
		);

		$backtrace->capture( $raw_backtrace = debug_backtrace() );

		$this->assertSame( count( $raw_backtrace ), count( $backtrace->frames() ) );

		foreach ( $backtrace->frames() as $frame ) {
			$this->assertTrue( is_a( $frame, Frame::class ) );
		}
	}

	#[Test]
	public function it_can_filter_backtrace_frames_using_frame_original_data(): void {
		$tags = new InMemoryFrameTags();

		$backtrace = new Backtrace(
			tags: $tags,
		);

		$backtrace->capture( debug_backtrace() );

		$filtered_backtrace = $backtrace->filter(
			function ( Frame $frame ) {
				$frame_function = $frame->get_function();

				if ( $frame_function instanceof FrameClassFunction ) {
					return self::class === $frame_function->get_class();
				}

				return false;
			}
		);

		$this->assertNotSame( $backtrace, $filtered_backtrace );
		$this->assertCount( 1, $filtered_backtrace->frames() );
	}
}
