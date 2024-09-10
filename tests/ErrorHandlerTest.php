<?php

declare(strict_types=1);

namespace Themosis\Components\Error\Tests;

use ErrorException;
use PHPUnit\Framework\Attributes\Test;
use Themosis\Components\Error\ErrorHandler;
use Themosis\Components\Error\InMemoryIssues;
use Themosis\Components\Error\InMemoryReporters;
use Themosis\Components\Error\Issue;
use Themosis\Components\Error\Reporters\CallbackReporter;
use Themosis\Components\Error\ReportHandler;

final class ErrorHandlerTest extends TestCase {
	protected function tearDown(): void {
		restore_error_handler();
	}

	#[Test]
	public function it_can_throw_error_exception_on_user_error_level(): void {
		$reporters = new InMemoryReporters();

		$error_handler = new ErrorHandler(
			report_handler: new ReportHandler(
				reporters: $reporters,
				issues: new InMemoryIssues(),
			),
		);

		set_error_handler( $error_handler );

		$this->expectException( ErrorException::class );
		$this->expectExceptionMessage( 'User Error' );

		trigger_error( 'User Error', E_USER_ERROR );
	}

	#[Test]
	public function it_can_throw_error_exception_on_user_notice_level(): void {
		$reporters = new InMemoryReporters();

		$error_handler = new ErrorHandler(
			report_handler: new ReportHandler(
				reporters: $reporters,
				issues: new InMemoryIssues(),
			),
		);

		set_error_handler( $error_handler );

		$this->expectException( ErrorException::class );
		$this->expectExceptionMessage( 'User Notice' );

		trigger_error( 'User Notice', E_USER_NOTICE );
	}

	#[Test]
	public function it_can_throw_error_exception_on_user_warning_level(): void {
		$reporters = new InMemoryReporters();

		$error_handler = new ErrorHandler(
			report_handler: new ReportHandler(
				reporters: $reporters,
				issues: new InMemoryIssues(),
			),
		);

		set_error_handler( $error_handler );

		$this->expectException( ErrorException::class );
		$this->expectExceptionMessage( 'User Warning' );

		trigger_error( 'User Warning', E_USER_WARNING );
	}

	#[Test]
	public function it_can_report_deprecated_errors(): void {
		$fake_log_message = '';

		$reporters = new InMemoryReporters();
		$reporters->add(
			reporter: new CallbackReporter(
				function ( Issue $issue ) {
					echo $issue->message();
				}
			),
		);
		$reporters->add(
			reporter: new CallbackReporter(
				function ( Issue $issue ) use ( &$fake_log_message ) {
					$fake_log_message = $issue->message();
				}
			),
		);

		$error_handler = new ErrorHandler(
			report_handler: new ReportHandler(
				reporters: $reporters,
				issues: new InMemoryIssues(),
			),
		);

		set_error_handler( $error_handler );

		$this->assertEmpty( $fake_log_message );

		ob_start();

		trigger_error( 'User Deprecated Error', E_USER_DEPRECATED );

		$stdout = ob_get_clean();

		$this->assertSame( 'User Deprecated Error', $stdout );
		$this->assertSame( 'User Deprecated Error', $fake_log_message );
	}
}
