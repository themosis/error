<?php

// SPDX-FileCopyrightText: 2024 Julien Lambé <julien@themosis.com>
//
// SPDX-License-Identifier: GPL-3.0-or-later

declare(strict_types=1);

namespace Themosis\Components\Error\Tests;

use Exception;
use PHPUnit\Framework\Attributes\Test;
use Themosis\Components\Error\Backtrace\Backtrace;
use Themosis\Components\Error\Backtrace\InMemoryFrameIdentifiers;
use Themosis\Components\Error\ExceptionHandlerHttpResponse;
use Themosis\Components\Error\InMemoryInformation;
use Themosis\Components\Error\InMemoryIssues;
use Themosis\Components\Error\InMemoryReporters;
use Themosis\Components\Error\Issue;
use Themosis\Components\Error\Reporters\CallbackReporter;
use Themosis\Components\Error\Reporters\Conditions\AlwaysReport;
use Themosis\Components\Error\ReportHandler;

final class ExceptionHandlerTest extends TestCase
{
    #[Test]
    public function it_can_report_thrown_exception(): void
    {
        $reporters = new InMemoryReporters();
        $reporters->add(
            condition: new AlwaysReport(),
            reporter: new CallbackReporter(
                static function (Issue $issue) {
                    $backtrace = new Backtrace(new InMemoryFrameIdentifiers());
                    $backtrace->capture_exception($issue->exception());

                    ( new ExceptionHandlerHttpResponse(
                        view_path: __DIR__ . '/../resources/views/exception.php',
                        backtrace: $backtrace,
                        information: new InMemoryInformation(),
                    ) )->render($issue);
                }
            ),
        );

        $report_handler = new ReportHandler(
            reporters: $reporters,
            issues: new InMemoryIssues(),
        );

        $report_handler->capture(
            Issue::from_exception(new Exception('Oops'))
        );

        ob_start();
            $report_handler->publish();
        $stdout = ob_get_clean();

        $this->assertTrue(str_contains($stdout, 'Oops'));
        $this->assertTrue(str_contains($stdout, 'Issue'));
    }
}
