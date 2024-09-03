<?php

namespace Themosis\Components\Error\Tests;

use DateTimeImmutable;
use Exception;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use PHPUnit\Framework\Attributes\Test;
use Themosis\Components\Error\Backtrace\Backtrace;
use Themosis\Components\Error\Backtrace\InMemoryFrameIdentifiers;
use Themosis\Components\Error\InMemoryReporters;
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

		$handler = new ReportHandler(
			reporters: $reporters,
		);

		$handler->report(
			exception: $exception = new Exception( 'There was an error...' ),
			occured_at: $date     = new DateTimeImmutable( 'now' ),
		);

		ob_start();
		$handler->release();
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

		$handler = new ReportHandler(
			reporters: $reporters,
		);

		$handler->report(
			exception: $exception = new Exception( 'There was an error...' ),
			occured_at: $date     = new DateTimeImmutable( 'now' ),
		);

		ob_start();
		$handler->release();
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
		$logger->pushHandler( new StreamHandler( $path = __DIR__ . '/test.log' ) );

		$reporters = new InMemoryReporters();
		$reporters->add( reporter: new LogReporter( $logger ) );

		$handler = new ReportHandler(
			reporters: $reporters,
		);

		$handler->report(
			exception: new Exception( 'Something went wrong!' ),
			occured_at: new DateTimeImmutable( 'now' ),
		);

		$handler->release();

		$this->assertTrue( file_exists( $path ) );
        $this->assertNotEmpty( file_get_contents( $path ) );

		unlink( $path );
	}
}
