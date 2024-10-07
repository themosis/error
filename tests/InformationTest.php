<?php

namespace Themosis\Components\Error\Tests;

use PHPUnit\Framework\Attributes\Test;
use Themosis\Components\Error\InformationGroup;
use Themosis\Components\Error\TextInfo;

final class InformationTest extends TestCase {
	#[Test]
	public function it_can_group_information(): void {
		$group = new InformationGroup(
			name: 'General',
		);

		$group
			->add( new TextInfo( 'PHP', '8.3.12' ) )
			->add( new TextInfo( 'Timezone', 'UTC' ) );

		$this->assertSame( 10, $group->priority() );
		$this->assertCount( 2, $group->get_information() );

        $info = $group->get_information()[0];

        $this->assertSame('PHP', $info->name());
        $this->assertSame('8.3.12', $info->value());

        $info = $group->get_information()[1];

        $this->assertSame('Timezone', $info->name());
        $this->assertSame('UTC', $info->value());
	}
}
