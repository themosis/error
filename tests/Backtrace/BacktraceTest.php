<?php

declare(strict_types=1);

namespace Themosis\Components\Error\Tests\Backtrace;

use PHPUnit\Framework\Attributes\Test;
use Themosis\Components\Error\Backtrace\Backtrace;
use Themosis\Components\Error\Backtrace\CustomFrameIdentifier;
use Themosis\Components\Error\Backtrace\CustomFrameTag;
use Themosis\Components\Error\Backtrace\Frame;
use Themosis\Components\Error\Backtrace\FrameClassFunction;
use Themosis\Components\Error\Backtrace\InMemoryFrameIdentifiers;
use Themosis\Components\Error\Backtrace\VendorFrameIdentifier;
use Themosis\Components\Error\Backtrace\VendorFrameTag;
use Themosis\Components\Error\Tests\TestCase;

final class BacktraceTest extends TestCase {
	#[Test]
	public function it_can_create_a_backtrace_using_php_debug_backtace(): void {
		$identifiers = new InMemoryFrameIdentifiers();

		$backtrace = new Backtrace(
			frame_identifiers: $identifiers,
		);

		$raw_backtrace = debug_backtrace();

		$backtrace->capture( $raw_backtrace );

		$this->assertSame( count( $raw_backtrace ), count( $backtrace->frames() ) );

		foreach ( $backtrace->frames() as $frame ) {
			$this->assertTrue( is_a( $frame, Frame::class ) );
		}
	}

	#[Test]
	public function it_can_filter_backtrace_frames_using_frame_original_data(): void {
		$identifiers = new InMemoryFrameIdentifiers();

		$backtrace = new Backtrace(
			frame_identifiers: $identifiers,
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

	#[Test]
	public function it_can_identify_vendor_frames_in_backtrace(): void {
		$identifiers = new InMemoryFrameIdentifiers();

		$identifiers->add(
			identifier: new VendorFrameIdentifier(
				project_root_path: dirname( __DIR__, 2 ),
			),
		);

		$test_file_tag = new CustomFrameTag(
			slug: 'test',
			name: 'Component Test File',
		);

		$identifiers->add(
			identifier: new CustomFrameIdentifier(
				tag: $test_file_tag,
				identifier: function ( Frame $frame ) {
					$frame_function = $frame->get_function();

					if ( $frame_function instanceof FrameClassFunction ) {
						return self::class === $frame_function->get_class();
					}

					return false;
				}
			),
		);

		$backtrace = ( new Backtrace(
			frame_identifiers: $identifiers,
		) )->capture( debug_backtrace() );

		$frames = $backtrace->frames();

		/** @var Frame $first_frame */
		$first_frame = $frames[0];

		$this->assertCount( 2, $first_frame->tags() );
		$this->assertTrue( $first_frame->is( new VendorFrameTag() ) );
		$this->assertTrue( $first_frame->is( $test_file_tag ) );

		/** @var Frame $second_frame */
		$second_frame = $frames[1];

		$this->assertCount( 1, $second_frame->tags() );
		$this->assertTrue( $second_frame->is( new VendorFrameTag() ) );
	}

	#[Test]
	public function it_can_render_a_backtrace_with_var_dump(): void {
		$identifiers = new InMemoryFrameIdentifiers();

		$backtrace = new Backtrace(
			frame_identifiers: $identifiers,
		);

		$backtrace->capture( debug_backtrace() );

		ob_start();

		var_dump( $backtrace );

		$output = ob_get_clean();

		$first_frame = $backtrace->frames()[0];

		$this->assertStringContainsString( (string) $first_frame, $output );
	}

	#[Test]
	public function it_can_generate_a_debug_backtrace_using_debug_named_constructor(): void {
		$backtrace = new Backtrace(
			frame_identifiers: new InMemoryFrameIdentifiers(),
		);

		$backtrace->capture( debug_backtrace() );

		$first_frame_backtrace = $backtrace->frames()[0];

		$debug_backtrace = Backtrace::debug();

		$first_frame_debug_backtrace = $debug_backtrace->frames()[0];

		$this->assertSame( (string) $first_frame_backtrace, (string) $first_frame_debug_backtrace );
		$this->assertSame( (string) $backtrace, (string) $debug_backtrace );
	}
}
