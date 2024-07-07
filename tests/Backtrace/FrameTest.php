<?php

declare(strict_types=1);

namespace Themosis\Components\Error\Tests\Backtrace;

use PHPUnit\Framework\Attributes\Test;
use Themosis\Components\Error\Backtrace\Frame;
use Themosis\Components\Error\Backtrace\FrameTag;
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

		foreach ( $frame as $key => $value ) {
			$this->assertSame( $data[ $key ], $value );
		}
	}

	#[Test]
	public function it_can_generate_a_backtrace_frame_for_class_with_instance_method(): void {
		$data = FramesProvider::class_with_instance_method();

		$frame = new Frame( $data );

		$this->assertSame(
			'/disk/web/project/app/Foo.php:18 App\Foo->bar()',
			(string) $frame,
		);

		foreach ( $frame as $key => $value ) {
			$this->assertSame( $data[ $key ], $value );
		}
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

	#[Test]
	public function it_can_create_a_backtrace_frame_and_assign_one_or_multiple_tags(): void {
		$data = FramesProvider::class_with_instance_method();

		$frame = new Frame( $data );

		$frame->add_tag(
			new FrameTag(
				slug: 'vendor',
				name: 'PHP Vendor',
			)
		);

		$frame->add_tag(
			new FrameTag(
				slug: 'themosis',
				name: 'Themosis Component',
			)
		);

		$frame->add_tag(
			new FrameTag(
				slug: 'test',
				name: 'Unit Test',
			)
		);

		$this->assertCount( 3, $frame->tags() );
	}

	#[Test]
	public function it_can_not_have_duplicate_tags_if_using_same_slugs(): void {
		$data = FramesProvider::class_with_instance_method();

		$frame = new Frame( $data );

		$frame->add_tag(
			$first_tag = new FrameTag(
				slug: 'vendor',
				name: 'Vendor',
			)
		);

		$frame->add_tag(
			$last_tag = new FrameTag(
				slug: 'vendor',
				name: 'Third-Party Package',
			),
		);

		$this->assertCount( 1, $frame->tags() );
		$this->assertSame(
			$last_tag->name(),
			$frame->tags()[ $first_tag->slug() ]->name(),
		);
	}
}
