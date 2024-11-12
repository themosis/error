<?php

// SPDX-FileCopyrightText: 2024 Julien Lambé <julien@themosis.com>
//
// SPDX-License-Identifier: GPL-3.0-or-later

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
use Themosis\Components\Error\Exceptions\DuplicateFrameIdentifier;
use Themosis\Components\Error\Tests\TestCase;

final class BacktraceTest extends TestCase
{
    #[Test]
    public function it_can_create_a_backtrace_using_php_debug_backtace(): void
    {
        $identifiers = new InMemoryFrameIdentifiers();

        $backtrace = new Backtrace(
            frameIdentifiers: $identifiers,
        );

        $rawBacktrace = debug_backtrace();

        $backtrace->capture($rawBacktrace);

        $this->assertSame(count($rawBacktrace), count($backtrace->frames()));

        foreach ($backtrace->frames() as $frame) {
            $this->assertTrue(is_a($frame, Frame::class));
        }
    }

    #[Test]
    public function it_can_filter_backtrace_frames_using_frame_original_data(): void
    {
        $identifiers = new InMemoryFrameIdentifiers();

        $backtrace = new Backtrace(
            frameIdentifiers: $identifiers,
        );

        $backtrace->capture(debug_backtrace());

        $filteredBacktrace = $backtrace->filter(
            function (Frame $frame) {
                $frameFunction = $frame->getFunction();

                if ($frameFunction instanceof FrameClassFunction) {
                    return self::class === $frameFunction->getClass();
                }

                return false;
            }
        );

        $this->assertNotSame($backtrace, $filteredBacktrace);
        $this->assertCount(1, $filteredBacktrace->frames());
    }

    #[Test]
    public function it_can_not_use_identifiers_with_identical_names(): void
    {
        $identifiers = new InMemoryFrameIdentifiers();

        $identifiers->add(new VendorFrameIdentifier(dirname(__DIR__, 2)));

        $this->expectException(DuplicateFrameIdentifier::class);

        $identifiers->add(new CustomFrameIdentifier(
            tag: new CustomFrameTag('vendor', 'Another Vendor'),
            identifier: function (Frame $frame) {
                return $frame->getFunction() == 'callme';
            }
        ));
    }

    #[Test]
    public function it_can_identify_vendor_frames_in_backtrace(): void
    {
        $identifiers = new InMemoryFrameIdentifiers();

        $identifiers->add(
            identifier: new VendorFrameIdentifier(
                projectRootPath: dirname(__DIR__, 2),
            ),
        );

        $testFileTag = new CustomFrameTag(
            slug: 'test',
            name: 'Component Test File',
        );

        $identifiers->add(
            identifier: new CustomFrameIdentifier(
                tag: $testFileTag,
                identifier: function (Frame $frame) {
                    $frameFunction = $frame->getFunction();

                    if ($frameFunction instanceof FrameClassFunction) {
                        return self::class === $frameFunction->getClass();
                    }

                    return false;
                }
            ),
        );

        $backtrace = ( new Backtrace(
            frameIdentifiers: $identifiers,
        ) )->capture(debug_backtrace());

        $frames = $backtrace->frames();

        /** @var Frame $first_frame */
        $first_frame = $frames[0];

        $this->assertCount(2, $first_frame->tags());
        $this->assertTrue($first_frame->is(new VendorFrameTag()));
        $this->assertTrue($first_frame->is($testFileTag));

        /** @var Frame $second_frame */
        $second_frame = $frames[1];

        $this->assertCount(1, $second_frame->tags());
        $this->assertTrue($second_frame->is(new VendorFrameTag()));
    }

    #[Test]
    public function it_can_render_a_backtrace_with_var_dump(): void
    {
        $identifiers = new InMemoryFrameIdentifiers();

        $backtrace = new Backtrace(
            frameIdentifiers: $identifiers,
        );

        $backtrace->capture(debug_backtrace());

        ob_start();

        var_dump($backtrace);

        $output = ob_get_clean();

        $firstFrame = $backtrace->frames()[0];

        $this->assertStringContainsString((string) $firstFrame, $output);
    }

    #[Test]
    public function it_can_generate_a_debug_backtrace_using_debug_named_constructor(): void
    {
        $backtrace = new Backtrace(
            frameIdentifiers: new InMemoryFrameIdentifiers(),
        );

        $backtrace->capture(debug_backtrace());

        $firstFrameBacktrace = $backtrace->frames()[0];

        $debugBacktrace = Backtrace::debug();

        $firstFrameDebugBacktrace = $debugBacktrace->frames()[0];

        $this->assertSame((string) $firstFrameBacktrace, (string) $firstFrameDebugBacktrace);
        $this->assertSame((string) $backtrace, (string) $debugBacktrace);
    }
}
