<?php

// SPDX-FileCopyrightText: 2024 Julien Lambé <julien@themosis.com>
//
// SPDX-License-Identifier: GPL-3.0-or-later

declare(strict_types=1);

namespace Themosis\Components\Error\Tests;

use PHPUnit\Framework\Attributes\Test;
use Themosis\Components\Error\InformationGroup;
use Themosis\Components\Error\InMemoryInformation;
use Themosis\Components\Error\TextInfo;

final class InformationTest extends TestCase
{
    #[Test]
    public function it_can_group_information(): void
    {
        $group = new InformationGroup(
            name: 'General',
        );

        $group
            ->add(new TextInfo('PHP', '8.3.12'))
            ->add(new TextInfo('Timezone', 'UTC'));

        $this->assertSame(10, $group->priority());
        $this->assertCount(2, $group->getInformation());

        $info = $group->getInformation()[0];

        $this->assertSame('PHP', $info->name());
        $this->assertSame('8.3.12', $info->value());

        $info = $group->getInformation()[1];

        $this->assertSame('Timezone', $info->name());
        $this->assertSame('UTC', $info->value());
    }

    #[Test]
    public function it_can_return_information_group_by_priority(): void
    {
        $group = new InformationGroup(
            name: 'General',
        );

        $group
            ->add(new TextInfo('PHP', '8.1.13'))
            ->add(new TextInfo('ENV', 'local'));

        $requestGroup = new InformationGroup(
            name: 'Request',
        );

        $requestGroup
            ->add(new TextInfo('Method', 'GET'))
            ->add(new TextInfo('Path', '/'))
            ->add(new TextInfo('Query', 'search=something'));

        $information = new InMemoryInformation();
        $information->add($group);
        $information->add($requestGroup);

        $result = $information->getInformationByPriority();

        $this->assertSame($group->name(), $result[0]->name());
        $this->assertSame($requestGroup->name(), $result[1]->name());
    }

    #[Test]
    public function it_can_return_information_group_by_priority_using_different_priority_values(): void
    {
        $group = new InformationGroup(
            name: 'General',
        );

        $group
            ->add(new TextInfo('PHP', '8.1.13'))
            ->add(new TextInfo('ENV', 'local'));

        $requestGroup = new InformationGroup(
            name: 'Request',
            priority: 5,
        );

        $requestGroup
            ->add(new TextInfo('Method', 'GET'))
            ->add(new TextInfo('Path', '/'))
            ->add(new TextInfo('Query', 'search=something'));

        $gitGroup = new InformationGroup(
            name: 'Git',
            priority: 15,
        );

        $gitGroup
            ->add(new TextInfo('Branch', 'main'))
            ->add(new TextInfo('User', 'You'));

        $information = new InMemoryInformation();
        $information->add($group);
        $information->add($requestGroup);
        $information->add($gitGroup);

        $result = $information->getInformationByPriority();

        $this->assertSame($requestGroup->name(), $result[0]->name());
        $this->assertSame($requestGroup->priority(), $result[0]->priority());

        $this->assertSame($group->name(), $result[1]->name());
        $this->assertSame($group->priority(), $result[1]->priority());

        $this->assertSame($gitGroup->name(), $result[2]->name());
        $this->assertSame($gitGroup->priority(), $result[2]->priority());
    }
}
