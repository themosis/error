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
use Themosis\Components\Error\ExceptionalIssue;
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
                    $backtrace->captureException($issue->exception());

                    ( new ExceptionHandlerHttpResponse(
                        backtrace: $backtrace,
                        information: new InMemoryInformation(),
                    ) )->render($issue);
                }
            ),
        );

        $reportHandler = new ReportHandler(
            reporters: $reporters,
            issues: new InMemoryIssues(),
        );

        $reportHandler->capture(
            ExceptionalIssue::create(new Exception('Oops'))
        );

        ob_start();
            $reportHandler->publish();
        $stdout = ob_get_clean();

        $this->assertTrue(str_contains($stdout, 'Oops'));
        $this->assertTrue(str_contains($stdout, 'Issue'));
    }
}
