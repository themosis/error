<?php

// SPDX-FileCopyrightText: 2024 Julien Lambé <julien@themosis.com>
//
// SPDX-License-Identifier: GPL-3.0-or-later

declare(strict_types=1);

namespace Themosis\Components\Error\Tests;

use ErrorException;
use PHPUnit\Framework\Attributes\Test;
use Themosis\Components\Error\ErrorHandler;
use Themosis\Components\Error\InMemoryIssues;
use Themosis\Components\Error\InMemoryReporters;
use Themosis\Components\Error\Issue;
use Themosis\Components\Error\Reporters\CallbackReporter;
use Themosis\Components\Error\Reporters\Conditions\AlwaysReport;
use Themosis\Components\Error\ReportHandler;

final class ErrorHandlerTest extends TestCase
{
    protected function tearDown(): void
    {
        restore_error_handler();
    }

    #[Test]
    public function it_can_throw_error_exception_on_user_error_level(): void
    {
        $reporters = new InMemoryReporters();

        $errorHandler = new ErrorHandler(
            reportHandler: new ReportHandler(
                reporters: $reporters,
                issues: new InMemoryIssues(),
            ),
        );

        set_error_handler($errorHandler);

        $this->expectException(ErrorException::class);
        $this->expectExceptionMessage('User Error');

        trigger_error('User Error', E_USER_ERROR);
    }

    #[Test]
    public function it_can_throw_error_exception_on_user_notice_level(): void
    {
        $reporters = new InMemoryReporters();

        $errorHandler = new ErrorHandler(
            reportHandler: new ReportHandler(
                reporters: $reporters,
                issues: new InMemoryIssues(),
            ),
        );

        set_error_handler($errorHandler);

        $this->expectException(ErrorException::class);
        $this->expectExceptionMessage('User Notice');

        trigger_error('User Notice', E_USER_NOTICE);
    }

    #[Test]
    public function it_can_throw_error_exception_on_user_warning_level(): void
    {
        $reporters = new InMemoryReporters();

        $errorHandler = new ErrorHandler(
            reportHandler: new ReportHandler(
                reporters: $reporters,
                issues: new InMemoryIssues(),
            ),
        );

        set_error_handler($errorHandler);

        $this->expectException(ErrorException::class);
        $this->expectExceptionMessage('User Warning');

        trigger_error('User Warning', E_USER_WARNING);
    }

    #[Test]
    public function it_can_report_deprecated_errors(): void
    {
        $fakeLogMessage = '';

        $reporters = new InMemoryReporters();
        $reporters->add(
            condition: new AlwaysReport(),
            reporter: new CallbackReporter(
                function (Issue $issue) {
                    echo $issue->message();
                }
            ),
        );
        $reporters->add(
            condition: new AlwaysReport(),
            reporter: new CallbackReporter(
                function (Issue $issue) use (&$fakeLogMessage) {
                    $fakeLogMessage = $issue->message();
                }
            ),
        );

        $errorHandler = new ErrorHandler(
            reportHandler: new ReportHandler(
                reporters: $reporters,
                issues: new InMemoryIssues(),
            ),
        );

        set_error_handler($errorHandler);

        $this->assertEmpty($fakeLogMessage);

        ob_start();

        trigger_error('User Deprecated Error', E_USER_DEPRECATED);

        $stdout = ob_get_clean();

        $this->assertSame('User Deprecated Error', $stdout);
        $this->assertSame('User Deprecated Error', $fakeLogMessage);
    }

    #[Test]
    public function it_can_be_ignored_if_current_reporting_is_disabled_on_user_notice(): void
    {
        $message = '';

        $reporters = new InMemoryReporters();

        $reporters->add(
            condition: new AlwaysReport(),
            reporter: new CallbackReporter(function (Issue $issue) use (&$message) {
                $message = $issue->message();
            }),
        );

        $errorHandler = new ErrorHandler(
            reportHandler: new ReportHandler(
                reporters: $reporters,
                issues: new InMemoryIssues(),
            ),
        );

        error_reporting(0);

        set_error_handler($errorHandler);

        ob_start();
            trigger_error('User error', E_USER_NOTICE);
        $stdout = ob_get_clean();

        $this->assertEmpty($message);
        $this->assertEmpty($stdout);
    }
}
