<?php

// SPDX-FileCopyrightText: 2024 Julien Lambé <julien@themosis.com>
//
// SPDX-License-Identifier: GPL-3.0-or-later

declare(strict_types=1);

namespace Themosis\Components\Error\Tests;

use DateTimeImmutable;
use Exception;
use PHPUnit\Framework\Attributes\Test;
use Themosis\Components\Error\Backtrace\Backtrace;
use Themosis\Components\Error\Backtrace\InMemoryFrameIdentifiers;
use Themosis\Components\Error\ExceptionalIssue;
use Themosis\Components\Error\InMemoryIssues;
use Themosis\Components\Error\InMemoryReporters;
use Themosis\Components\Error\Issue;
use Themosis\Components\Error\Reporters\AlwaysStopAfter;
use Themosis\Components\Error\Reporters\CallbackReporter;
use Themosis\Components\Error\Reporters\Conditions\AlwaysReport;
use Themosis\Components\Error\Reporters\Conditions\CallbackCondition;
use Themosis\Components\Error\Reporters\LogReporter;
use Themosis\Components\Error\Reporters\StdoutReporter;
use Themosis\Components\Error\ReportHandler;

final class ReportHandlerTest extends TestCase
{
    #[Test]
    public function it_can_report_an_issue_on_single_registered_reporter(): void
    {
        $frameIdentifiers = new InMemoryFrameIdentifiers();
        $backtrace = new Backtrace($frameIdentifiers);

        $reporters = new InMemoryReporters();
        $reporters->add(
            condition: new AlwaysReport(),
            reporter: new StdoutReporter($backtrace)
        );

        $issue = ExceptionalIssue::create(
            exception: $exception = new Exception('There was an error...'),
            occuredAt: $date = new DateTimeImmutable('now'),
        );

        $issues = new InMemoryIssues();
        $issues->add($issue);

        $handler = new ReportHandler(
            reporters: $reporters,
            issues: $issues,
        );

        ob_start();
        $handler->publish();
        $stdout = ob_get_clean();

        $expected = sprintf(
            "[%s] %s\n%s\n",
            $date->format(DateTimeImmutable::W3C),
            $exception->getMessage(),
            (string) $backtrace
        );

        $this->assertSame($expected, $stdout);
    }

    #[Test]
    public function it_can_report_an_issue_on_multiple_registered_reporters(): void
    {
        $frameIdentifiers = new InMemoryFrameIdentifiers();
        $backtrace = new Backtrace($frameIdentifiers);

        $reporters = new InMemoryReporters();
        $reporters->add(
            condition: new AlwaysReport(),
            reporter: new StdoutReporter($backtrace)
        );
        $reporters->add(
            condition: new AlwaysReport(),
            reporter: new StdoutReporter($backtrace)
        );
        $reporters->add(
            condition: new AlwaysReport(),
            reporter: new StdoutReporter($backtrace)
        );

        $issues = new InMemoryIssues();
        $issues->add(
            issue: ExceptionalIssue::create(
                exception: $exception = new Exception('There was an error...'),
                occuredAt: $date = new DateTimeImmutable('now'),
            ),
        );

        $handler = new ReportHandler(
            reporters: $reporters,
            issues: $issues,
        );

        ob_start();
        $handler->publish();
        $stdout = ob_get_clean();

        $expected = sprintf(
            "[%s] %s\n%s\n",
            $date->format(DateTimeImmutable::W3C),
            $exception->getMessage(),
            (string) $backtrace
        );

        $this->assertSame(implode('', [$expected, $expected, $expected]), $stdout);
    }

    #[Test]
    public function it_can_report_an_issue_to_local_log_file_on_error_level(): void
    {
        $log = [];
        $logger = new FakePsrLogger($log);

        $reporters = new InMemoryReporters();
        $reporters->add(
            condition: new AlwaysReport(),
            reporter: new LogReporter($logger)
        );

        $issues = new InMemoryIssues();
        $issues->add(
            issue: ExceptionalIssue::create(
                exception: $exception = new FakeException('Something went wrong!'),
                occuredAt: new DateTimeImmutable('now'),
            ),
        );

        $handler = new ReportHandler(
            reporters: $reporters,
            issues: $issues,
        );

        $handler->publish();

        $this->assertCount(1, $log);
        $this->assertSame([
            [
                'level' => 'error',
                'message' => $exception->getMessage(),
                'context' => $exception->information()->toArray(),
            ]
        ], $log);
    }

    #[Test]
    public function it_can_report_an_issue_with_custom_error_level_to_local_log_file(): void
    {
        $log = [];
        $logger = new FakePsrLogger($log);

        $reporters = new InMemoryReporters();
        $reporters->add(
            condition: new AlwaysReport(),
            reporter: new LogReporter($logger)
        );

        $issues = new InMemoryIssues();
        $issues->add(
            issue: ExceptionalIssue::create(
                exception: $exception = new FakeNoticeException('A gentle notice'),
                occuredAt: new DateTimeImmutable('now'),
            ),
        );

        $handler = new ReportHandler(
            reporters: $reporters,
            issues: $issues,
        );

        $handler->publish();

        $this->assertCount(1, $log);
        $this->assertSame([
            [
                'level' => 'notice',
                'message' => $exception->getMessage(),
                'context' => [],
            ]
        ], $log);
    }

    #[Test]
    public function it_can_report_multiple_issues_on_single_reporter(): void
    {
        $frameIdentifiers = new InMemoryFrameIdentifiers();
        $backtrace = new Backtrace($frameIdentifiers);

        $reporters = new InMemoryReporters();
        $reporters->add(
            condition: new AlwaysReport(),
            reporter: new StdoutReporter($backtrace)
        );

        $issues = new InMemoryIssues();
        $issues->add(
            issue: ExceptionalIssue::create(
                exception: $exception_a = new Exception('Error AAA'),
                occuredAt: $date_a = new DateTimeImmutable('2 days ago'),
            ),
        );
        $issues->add(
            issue: ExceptionalIssue::create(
                exception: $exception_b = new Exception('Error BBB'),
                occuredAt: $date_b = new DateTimeImmutable('now'),
            ),
        );

        $handler = new ReportHandler(
            reporters: $reporters,
            issues: $issues,
        );

        ob_start();
        $handler->publish();
        $stdout = ob_get_clean();

        $expectedA = sprintf(
            "[%s] %s\n%s\n",
            $date_a->format(DateTimeImmutable::W3C),
            $exception_a->getMessage(),
            (string) $backtrace
        );

        $expectedB = sprintf(
            "[%s] %s\n%s\n",
            $date_b->format(DateTimeImmutable::W3C),
            $exception_b->getMessage(),
            (string) $backtrace
        );

        $this->assertSame(implode('', [$expectedA, $expectedB]), $stdout);
    }

    #[Test]
    public function it_can_report_multiple_issues_on_multiple_reporters(): void
    {
        $frameIdentifiers = new InMemoryFrameIdentifiers();
        $backtrace = new Backtrace($frameIdentifiers);

        $log = [];
        $logger = new FakePsrLogger($log);

        $reporters = new InMemoryReporters();
        $reporters->add(
            condition: new AlwaysReport(),
            reporter: new StdoutReporter($backtrace)
        );
        $reporters->add(
            condition: new AlwaysReport(),
            reporter: new LogReporter($logger)
        );

        $issues = new InMemoryIssues();
        $issues->add(
            issue: ExceptionalIssue::create(
                exception: $exceptionA = new Exception('Error AAA'),
                occuredAt: new DateTimeImmutable('2 days ago'),
            ),
        );
        $issues->add(
            issue: ExceptionalIssue::create(
                exception: $exceptionB = new Exception('Error BBB'),
                occuredAt: new DateTimeImmutable('now'),
            ),
        );

        $handler = new ReportHandler(
            reporters: $reporters,
            issues: $issues,
        );

        ob_start();
        $handler->publish();
        $stdout = ob_get_clean();

        preg_match_all('/Error AAA/', $stdout, $matchesA);
        preg_match_all('/Error BBB/', $stdout, $matchesB);

        $this->assertCount(1, array_shift($matchesA));
        $this->assertCount(1, array_shift($matchesB));

        $this->assertCount(2, $log);
        $this->assertSame([
            [
                'level' => 'error',
                'message' => $exceptionA->getMessage(),
                'context' => [],
            ],
            [
                'level' => 'error',
                'message' => $exceptionB->getMessage(),
                'context' => [],
            ],
        ], $log);
    }

    #[Test]
    public function it_can_report_an_issue_using_a_closure(): void
    {
        $reported = false;
        $reportedMessage = '';

        $reporters = new InMemoryReporters();
        $reporters->add(
            condition: new AlwaysReport(),
            reporter: new CallbackReporter(
                static function (Issue $issue) use (&$reported, &$reportedMessage) {
                    $reported = true;
                    $reportedMessage = $issue->message();
                }
            )
        );

        $issues = new InMemoryIssues();
        $issues->add(
            issue: ExceptionalIssue::create(
                exception: new Exception('Oops!'),
            ),
        );

        $handler = new ReportHandler(
            reporters: $reporters,
            issues: $issues,
        );

        $this->assertFalse($reported);
        $this->assertEmpty($reportedMessage);

        $handler->publish();

        $this->assertTrue($reported);
        $this->assertSame('Oops!', $reportedMessage);
    }

    #[Test]
    public function it_can_report_only_with_allowed_reporters(): void
    {
        $reporters = new InMemoryReporters();
        $reporters->add(
            condition: new CallbackCondition(static fn() => false),
            reporter: new CallbackReporter(
                function (Issue $issue) {
                    echo 'Should Not Be Reported!';
                    echo $issue->message();
                }
            ),
        );
        $reporters->add(
            condition: new CallbackCondition(
                function (Issue $issue) {
                    return $issue->exception() instanceof Exception;
                }
            ),
            reporter: new CallbackReporter(
                function (Issue $issue) {
                    echo "This is a reported issue.\n";
                    echo $issue->message();
                }
            ),
        );

        $handler = new ReportHandler(
            reporters: $reporters,
            issues: new InMemoryIssues(),
        );

        $handler->capture(ExceptionalIssue::create(new Exception('Oops!')));

        ob_start();
        $handler->publish();
        $stdout = ob_get_clean();

        $this->assertSame("This is a reported issue.\nOops!", $stdout);
    }

    #[Test]
    public function it_can_not_propagate_report_if_reporter_can_be_stopped(): void
    {
        $reporters = new InMemoryReporters();
        $reporters->add(
            condition: new AlwaysReport(),
            reporter: new AlwaysStopAfter(
                new CallbackReporter(
                    function (Issue $issue) {
                        echo "Only this is reported.\n";
                        echo $issue->message();
                    }
                )
            ),
        );
        $reporters->add(
            condition: new AlwaysReport(),
            reporter: new CallbackReporter(
                function (Issue $issue) {
                    echo "This should not be reported.\n";
                    echo $issue->message();
                }
            ),
        );

        $handler = new ReportHandler(
            reporters: $reporters,
            issues: new InMemoryIssues(),
        );

        $handler->capture(ExceptionalIssue::create(new Exception('Oops!')));

        ob_start();
        $handler->publish();
        $stdout = ob_get_clean();

        $this->assertSame("Only this is reported.\nOops!", $stdout);
    }
}
