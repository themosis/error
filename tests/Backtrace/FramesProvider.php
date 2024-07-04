<?php

declare(strict_types=1);

namespace Themosis\Components\Error\Tests\Backtrace;

final class FramesProvider {
	public static function class_with_static_method(): array {
		return [
			'file'     => '/disk/web/project/app/Something.php',
			'line'     => 22,
			'function' => 'create',
			'class'    => 'App\Something',
			'type'     => '::',
			'args'     => [],
		];
	}

	public static function file_with_include(): array {
		return [
			'file'     => '/disk/web/project/app/config.php',
			'line'     => 11,
			'args'     => [
				[ '/disk/web/project/inc/app.php' ],
			],
			'function' => 'include',
		];
	}
}
