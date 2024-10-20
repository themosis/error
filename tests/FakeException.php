<?php

// SPDX-FileCopyrightText: 2024 Julien Lambé <julien@themosis.com>
//
// SPDX-License-Identifier: GPL-3.0-or-later

declare(strict_types=1);

namespace Themosis\Components\Error\Tests;

use Exception;
use Themosis\Components\Error\AdditionalInformation;
use Themosis\Components\Error\InformationGroup;
use Themosis\Components\Error\TextInfo;

final class FakeException extends Exception implements AdditionalInformation {
	public function information(): InformationGroup {
		return ( new InformationGroup( 'Order' ) )
			->add( new TextInfo( 'Order ID', 'ORD-1234' ) )
			->add( new TextInfo( 'Customer ID', 'USR-1234' ) );
	}
}
