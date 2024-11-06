<?php

// SPDX-FileCopyrightText: 2024 Julien Lambé <julien@themosis.com>
//
// SPDX-License-Identifier: GPL-3.0-or-later

declare(strict_types=1);

namespace Themosis\Components\Error\Tests\Backtrace;

final class FramesProvider
{
    public static function classWithStaticMethod(): array
    {
        return [
            'file'     => '/disk/web/project/app/Something.php',
            'line'     => 22,
            'function' => 'create',
            'class'    => 'App\Something',
            'type'     => '::',
            'args'     => [],
        ];
    }

    public static function classWithInstanceMethod(): array
    {
        return [
            'file'     => '/disk/web/project/app/Foo.php',
            'line'     => 18,
            'function' => 'bar',
            'class'    => 'App\Foo',
            'type'     => '->',
        ];
    }

    public static function fileWithInclude(): array
    {
        return [
            'file'     => '/disk/web/project/app/config.php',
            'line'     => 11,
            'args'     => [
                [ '/disk/web/project/inc/app.php' ],
            ],
            'function' => 'include',
        ];
    }

    public static function vendorClassWithInstanceMethod(): array
    {
        return [
            'file'     => '/disk/web/project/vendor/third-party/some-package/Runner.php',
            'line'     => 79,
            'function' => 'run',
            'class'    => 'ThirdParty\SomePackage\Runner',
            'type'     => '->',
        ];
    }

    public static function invokeMagicMethod(): array
    {
        return [
            'function' => '__invoke',
            'class' => 'SomeVendor\AwesomePackage\Handler',
            'type' => '->',
            'args' => [256, 'Something went wrong', '/disk/web/project/vendor/some-vendor/awesome/Handler.php', 68]
        ];
    }

    public static function objectError(): array
    {
        return [
            'file'     => '/disk/web/project/module/orders/Order.php',
            'line'     => 113,
            'function' => 'checkout',
            'class'    => 'App\Module\Orders\Order',
            'object'   => new class() { public function checkout() {} },
            'type'     => '->',
        ];
    }

    public static function phpFunctionInvalidArguments(): array
    {
        return [
            'file'     => '/disk/web/project/helpers.php',
            'line'     => 6,
            'function' => 'array_filter',
            'args'     => [42],
        ];
    }
}
