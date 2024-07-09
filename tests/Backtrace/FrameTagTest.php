<?php

declare(strict_types=1);

namespace Themosis\Components\Error\Tests\Backtrace;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Themosis\Components\Error\Backtrace\CustomFrameTag;
use Themosis\Components\Error\Exceptions\InvalidFrameTagArgument;
use Themosis\Components\Error\Tests\TestCase;

final class FrameTagTest extends TestCase {
	#[Test]
	#[DataProvider( 'invalidTags' )]
	public function it_can_not_create_a_frame_tag_with_invalid_arguments( string $slug, string $name ): void {
		$this->expectException( InvalidFrameTagArgument::class );

		new CustomFrameTag(
			slug: $slug,
			name: $name,
		);
	}

	public static function invalidTags(): array {
		return [
			'Mix uppercase and lowercase with whitespace' => [ 'Invalid Slug', 'Invalid' ],
			'Lowercase with dash'                         => [ 'invalid-slug', 'Invalid' ],
			'Lowercase with whitespace'                   => [ 'invalid slug', 'Invalid' ],
			'Lowercase with numbers'                      => [ 'inval1d_3lug', 'Invalid' ],
			'Lowercase with dots'                         => [ 'invalid.slug', 'Invalid' ],
			'Empty slug'                                  => [ '', 'Invalid' ],
			'Empty name'                                  => [ 'invalid', '' ],
		];
	}
}
