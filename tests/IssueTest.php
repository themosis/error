<?php

// SPDX-FileCopyrightText: 2024 Julien Lambé <julien@themosis.com>
//
// SPDX-License-Identifier: GPL-3.0-or-later

declare(strict_types=1);

namespace Themosis\Components\Error\Tests;

use PHPUnit\Framework\Attributes\Test;
use Themosis\Components\Error\Issue;

final class IssueTest extends TestCase {
	#[Test]
	public function it_can_contain_additional_information(): void {
		$exception = new FakeException( 'Something went wrong with the order.' );

		$issue = Issue::from_exception( $exception );

		$information = $issue->info();

		$this->assertNotNull( $information );

		$infos = $information->get_information();

		$this->assertCount( 2, $infos );

		$order_id = $infos[0];

		$this->assertSame( 'Order ID', $order_id->name() );
		$this->assertSame( 'ORD-1234', $order_id->value() );

		$customer_id = $infos[1];

		$this->assertSame( 'Customer ID', $customer_id->name() );
		$this->assertSame( 'USR-1234', $customer_id->value() );
	}
}
