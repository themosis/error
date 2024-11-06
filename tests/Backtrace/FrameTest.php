<?php

// SPDX-FileCopyrightText: 2024 Julien Lambé <julien@themosis.com>
//
// SPDX-License-Identifier: GPL-3.0-or-later

declare(strict_types=1);

namespace Themosis\Components\Error\Tests\Backtrace;

use PHPUnit\Framework\Attributes\Test;
use Themosis\Components\Error\Backtrace\AppFrameTag;
use Themosis\Components\Error\Backtrace\CustomFrameTag;
use Themosis\Components\Error\Backtrace\Frame;
use Themosis\Components\Error\Backtrace\VendorFrameIdentifier;
use Themosis\Components\Error\Backtrace\VendorFrameTag;
use Themosis\Components\Error\Tests\TestCase;

final class FrameTest extends TestCase
{
    #[Test]
    public function it_can_generate_a_backtrace_frame_for_class_with_static_method(): void
    {
        $data = FramesProvider::classWithStaticMethod();

        $frame = new Frame($data);

        $this->assertSame(
            '/disk/web/project/app/Something.php:22 App\Something::create()',
            (string) $frame,
        );

        foreach ($frame as $key => $value) {
            $this->assertSame($data[ $key ], $value);
        }

        $this->assertSame($data['file'], $frame->getFile()?->path());
        $this->assertSame($data['line'], $frame->getFile()?->line());
        $this->assertSame($data['function'], $frame->getFunction()->getName());
        $this->assertEmpty($frame->getArgs());
        $this->assertNull($frame->getObject());
    }

    #[Test]
    public function it_can_generate_a_backtrace_frame_for_class_with_instance_method(): void
    {
        $data = FramesProvider::classWithInstanceMethod();

        $frame = new Frame($data);

        $this->assertSame(
            '/disk/web/project/app/Foo.php:18 App\Foo->bar()',
            (string) $frame,
        );

        foreach ($frame as $key => $value) {
            $this->assertSame($data[ $key ], $value);
        }
    }

    #[Test]
    public function it_can_generate_a_backtrace_frame_for_file_with_include(): void
    {
        $data = FramesProvider::fileWithInclude();

        $frame = new Frame($data);

        $this->assertSame(
            '/disk/web/project/app/config.php:11 include()',
            (string) $frame,
        );

        $this->assertSame($data['function'], $frame->getFunction()->getName());
    }

    #[Test]
    public function it_can_generate_a_backtrace_frame_for_invalid_php_function_usage(): void
    {
        $data = FramesProvider::phpFunctionInvalidArguments();

        $frame = new Frame($data);

        $this->assertSame($data['function'], $frame->getFunction()->getName());
        $this->assertCount(1, $frame->getArgs());
        $this->assertSame(42, $frame->getArgs()[0]);
    }

    #[Test]
    public function it_can_create_a_backtrace_frame_and_assign_one_or_multiple_tags(): void
    {
        $data = FramesProvider::classWithInstanceMethod();

        $frame = new Frame($data);

        $frame->addTag(
            new CustomFrameTag(
                slug: 'vendor',
                name: 'PHP Vendor',
            )
        );

        $frame->addTag(
            new CustomFrameTag(
                slug: 'themosis',
                name: 'Themosis Component',
            )
        );

        $frame->addTag(
            new CustomFrameTag(
                slug: 'test',
                name: 'Unit Test',
            )
        );

        $this->assertCount(3, $frame->tags());
    }

    #[Test]
    public function it_can_create_invoke_magic_method_frame(): void
    {
        $data = FramesProvider::invokeMagicMethod();

        $frame = new Frame($data);

        $this->assertSame('SomeVendor\AwesomePackage\Handler->__invoke()', (string) $frame);
        $this->assertNull($frame->getFile());
        $this->assertSame($data['function'], $frame->getFunction()->getName());
    }

    #[Test]
    public function it_can_create_frame_with_object_instance_error(): void
    {
        $data = FramesProvider::objectError();

        $frame = new Frame($data);

        $obj = $frame->getObject();

        $this->assertTrue(is_object($obj));
        $this->assertTrue(method_exists($obj, 'checkout'));
    }

    #[Test]
    public function it_can_not_have_duplicate_tags_if_using_same_slugs(): void
    {
        $data = FramesProvider::classWithInstanceMethod();

        $frame = new Frame($data);

        $first_tag = new CustomFrameTag(
            slug: 'vendor',
            name: 'Vendor',
        );

        $frame->addTag($first_tag);

        $last_tag = new CustomFrameTag(
            slug: 'vendor',
            name: 'Third-Party Package',
        );

        $frame->addTag($last_tag);

        $this->assertCount(1, $frame->tags());
        $this->assertSame(
            $last_tag->name(),
            $frame->tags()[ $first_tag->slug() ]->name(),
        );
    }

    #[Test]
    public function it_can_identify_a_frame_using_tag(): void
    {
        $data = FramesProvider::classWithStaticMethod();

        $frame = new Frame($data);

        $appTag = new AppFrameTag();

        $frame->addTag($appTag);

        $packageTag = new VendorFrameTag();

        $frame->addTag($packageTag);

        $this->assertTrue($frame->is($appTag));
        $this->assertTrue($frame->is(new CustomFrameTag(slug: 'app', name: 'Application')));
        $this->assertFalse($frame->is(new CustomFrameTag(slug: 'app', name: 'App')));

        $this->assertTrue($frame->is($packageTag));
        $this->assertFalse($frame->is(new CustomFrameTag(slug: 'vendor', name: 'Package')));
    }

    #[Test]
    public function it_can_identify_a_vendor_frame(): void
    {
        $data = FramesProvider::vendorClassWithInstanceMethod();

        $frame = new Frame($data);

        $identifier = new VendorFrameIdentifier(
            projectRootPath: '/disk/web/project',
        );

        $this->assertTrue($identifier->identify($frame));
    }

    #[Test]
    public function it_can_not_identify_a_frame_as_vendor_if_no_file_or_vendor_path(): void
    {
        $data = FramesProvider::invokeMagicMethod();

        $frame = new Frame($data);

        $identifier = new VendorFrameIdentifier(
            projectRootPath: '/disk/web/project',
        );

        $this->assertFalse($identifier->identify($frame));

        $data = FramesProvider::fileWithInclude();

        $frame = new Frame($data);

        $this->assertFalse($identifier->identify($frame));
    }
}
