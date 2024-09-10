<?php

declare(strict_types=1);

namespace Themosis\Components\Error\Tests;

use DateTimeImmutable;
use Exception;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use PHPUnit\Framework\Attributes\Test;
use Themosis\Components\Error\Backtrace\Backtrace;
use Themosis\Components\Error\Backtrace\InMemoryFrameIdentifiers;
use Themosis\Components\Error\InMemoryIssues;
use Themosis\Components\Error\InMemoryReporters;
use Themosis\Components\Error\Issue;
use Themosis\Components\Error\Reporters\CallbackReporter;
use Themosis\Components\Error\Reporters\LogReporter;
use Themosis\Components\Error\Reporters\StdoutReporter;
use Themosis\Components\Error\ReportHandler;

final class ReportHandlerTest extends TestCase {
	#[Test]
	public function it_can_report_an_issue_on_single_registered_reporter(): void {
		$frame_identifiers = new InMemoryFrameIdentifiers();
		$backtrace         = new Backtrace( $frame_identifiers );

		$reporters = new InMemoryReporters();
		$reporters->add(
			reporter: new StdoutReporter( $backtrace )
		);

		$issue                    = Issue::from_exception(
			exception: $exception = new Exception( 'There was an error...' ),
			occured_at: $date     = new DateTimeImmutable( 'now' ),
		);

		$issues = new InMemoryIssues();
		$issues->add( $issue );

		$handler = new ReportHandler(
			reporters: $reporters,
			issues: $issues,
		);

		ob_start();
		$handler->publish();
		$stdout = ob_get_clean();

		$expected = sprintf(
			"[%s] %s\n%s\n",
			$date->format( DateTimeImmutable::W3C ),
			$exception->getMessage(),
			(string) $backtrace
		);

		$this->assertSame( $expected, $stdout );
	}

	#[Test]
	public function it_can_report_an_issue_on_multiple_registered_reporters(): void {
		$frame_identifiers = new InMemoryFrameIdentifiers();
		$backtrace         = new Backtrace( $frame_identifiers );

		$reporters = new InMemoryReporters();
		$reporters->add( reporter: new StdoutReporter( $backtrace ) );
		$reporters->add( reporter: new StdoutReporter( $backtrace ) );
		$reporters->add( reporter: new StdoutReporter( $backtrace ) );

		$issues                       = new InMemoryIssues();
		$issues->add(
			issue: Issue::from_exception(
				exception: $exception = new Exception( 'There was an error...' ),
				occured_at: $date     = new DateTimeImmutable( 'now' ),
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
			$date->format( DateTimeImmutable::W3C ),
			$exception->getMessage(),
			(string) $backtrace
		);

		$this->assertSame( implode( '', [ $expected, $expected, $expected ] ), $stdout );
	}

	#[Test]
	public function it_can_report_an_issue_to_local_log_file(): void {
		$logger = new Logger( 'APP' );
		$logger->pushHandler( new StreamHandler( stream: $path = __DIR__ . '/test.log' ) );

		$reporters = new InMemoryReporters();
		$reporters->add( reporter: new LogReporter( $logger ) );

		$issues = new InMemoryIssues();
		$issues->add(
			issue: Issue::from_exception(
				exception: new Exception( 'Something went wrong!' ),
				occured_at: new DateTimeImmutable( 'now' ),
			),
		);

		$handler = new ReportHandler(
			reporters: $reporters,
			issues: $issues,
		);

		$handler->publish();

		$this->assertTrue( file_exists( $path ) );
		$this->assertNotEmpty( file_get_contents( $path ) );

		unlink( $path );
	}

	#[Test]
	public function it_can_report_multiple_issues_on_single_reporter(): void {
		$frame_identifiers = new InMemoryFrameIdentifiers();
		$backtrace         = new Backtrace( $frame_identifiers );

		$reporters = new InMemoryReporters();
		$reporters->add( reporter: new StdoutReporter( $backtrace ) );

		$issues                         = new InMemoryIssues();
		$issues->add(
			issue: Issue::from_exception(
				exception: $exception_a = new Exception( 'Error AAA' ),
				occured_at: $date_a     = new DateTimeImmutable( '2 days ago' ),
			),
		);
		$issues->add(
			issue: Issue::from_exception(
				exception: $exception_b = new Exception( 'Error BBB' ),
				occured_at: $date_b     = new DateTimeImmutable( 'now' ),
			),
		);

		$handler = new ReportHandler(
			reporters: $reporters,
			issues: $issues,
		);

		ob_start();
		$handler->publish();
		$stdout = ob_get_clean();

		$expected_a = sprintf(
			"[%s] %s\n%s\n",
			$date_a->format( DateTimeImmutable::W3C ),
			$exception_a->getMessage(),
			(string) $backtrace
		);

		$expected_b = sprintf(
			"[%s] %s\n%s\n",
			$date_b->format( DateTimeImmutable::W3C ),
			$exception_b->getMessage(),
			(string) $backtrace
		);

		$this->assertSame( implode( '', [ $expected_a, $expected_b ] ), $stdout );
	}

	#[Test]
	public function it_can_report_multiple_issues_on_multiple_reporters(): void {
		$frame_identifiers = new InMemoryFrameIdentifiers();
		$backtrace         = new Backtrace( $frame_identifiers );

		$logger = new Logger( 'TEST' );
		$logger->pushHandler( new StreamHandler( 'php://output' ) );

		$reporters = new InMemoryReporters();
		$reporters->add( reporter: new StdoutReporter( $backtrace ) );
		$reporters->add( reporter: new LogReporter( $logger ) );

		$issues = new InMemoryIssues();
		$issues->add(
			issue: Issue::from_exception(
				exception: new Exception( 'Error AAA' ),
				occured_at: new DateTimeImmutable( '2 days ago' ),
			),
		);
		$issues->add(
			issue: Issue::from_exception(
				exception: new Exception( 'Error BBB' ),
				occured_at: new DateTimeImmutable( 'now' ),
			),
		);

		$handler = new ReportHandler(
			reporters: $reporters,
			issues: $issues,
		);

		ob_start();
		$handler->publish();
		$stdout = ob_get_clean();

		preg_match_all( '/Error AAA/', $stdout, $matches_a );
		preg_match_all( '/Error BBB/', $stdout, $matches_b );

		$this->assertCount( 2, array_shift( $matches_a ) );
		$this->assertCount( 2, array_shift( $matches_b ) );
	}

	#[Test]
	public function it_can_report_an_issue_using_a_closure(): void {
		$reported         = false;
		$reported_message = '';

		$reporters = new InMemoryReporters();
		$reporters->add(
			reporter: new CallbackReporter(
				static function ( Issue $issue ) use ( &$reported, &$reported_message ) {
					$reported         = true;
					$reported_message = $issue->message();
				}
			)
		);

		$issues = new InMemoryIssues();
		$issues->add(
			issue: Issue::from_exception(
				exception: new Exception( 'Oops!' ),
			),
		);

		$handler = new ReportHandler(
			reporters: $reporters,
			issues: $issues,
		);

		$this->assertFalse( $reported );
		$this->assertEmpty( $reported_message );

		$handler->publish();

		$this->assertTrue( $reported );
		$this->assertSame( 'Oops!', $reported_message );
	}
}
