<?php

// SPDX-FileCopyrightText: 2024 Julien Lambé <julien@themosis.com>
//
// SPDX-License-Identifier: GPL-3.0-or-later

declare(strict_types=1);

namespace Themosis\Components\Error\Tests\Backtrace;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Themosis\Components\Error\Backtrace\File;
use Themosis\Components\Error\Backtrace\FilePreview;

final class FilePreviewTest extends TestCase
{
    #[Test]
    public function it_can_preview_file_content_that_starts_close_to_beginning_of_a_file(): void
    {
        $preview = new FilePreview(
            file: new File(
                filepath: __FILE__,
                line: 3,
            ),
        );

        $this->assertCount(5, $preview->get_lines());
    }

    #[Test]
    public function it_can_preview_file_content_that_starts_close_to_end_of_a_file(): void
    {
        $filepath = __DIR__ . '/fixtures/file-with-errors.php';

        $preview = new FilePreview(
            file: new File(
                filepath: $filepath,
                line: 15,
            ),
        );

        $this->assertCount(7, $preview->get_lines());
    }
}
