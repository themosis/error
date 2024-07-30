<?php

declare(strict_types=1);

namespace Themosis\Components\Error\Tests;

use DateTimeImmutable;
use DateTimeZone;
use PHPUnit\Framework\Attributes\Test;
use Themosis\Components\Error\IssueDate;

final class IssueDateTest extends TestCase {
	#[Test]
	public function it_can_format_issue_date_with_default_format(): void {
		$date = new IssueDate(
			datetime: DateTimeImmutable::createFromFormat(
				'Y-m-d H:i:s',
				'2024-07-10 13:42:18',
				new DateTimeZone( 'Europe/Brussels' ),
			),
		);

		$this->assertSame( '2024-07-10T13:42:18+02:00', $date->as_string() );
	}

	#[Test]
	public function it_can_format_issue_date_with_custom_format(): void {
		$date = ( new IssueDate(
			datetime: DateTimeImmutable::createFromFormat(
				'Y-m-d H:i:s',
				'2024-07-10 13:42:18',
				new DateTimeZone( 'Europe/Brussels' ),
			),
		) )->with_format( 'Y/m/d' );

		$this->assertSame( '2024/07/10', $date->as_string() );
	}
}
