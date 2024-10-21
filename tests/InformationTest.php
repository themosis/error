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
        $this->assertCount(2, $group->get_information());

        $info = $group->get_information()[0];

        $this->assertSame('PHP', $info->name());
        $this->assertSame('8.3.12', $info->value());

        $info = $group->get_information()[1];

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

        $request_group = new InformationGroup(
            name: 'Request',
        );

        $request_group
            ->add(new TextInfo('Method', 'GET'))
            ->add(new TextInfo('Path', '/'))
            ->add(new TextInfo('Query', 'search=something'));

        $information = new InMemoryInformation();
        $information->add($group);
        $information->add($request_group);

        $result = $information->get_information_by_priority();

        $this->assertSame($group->name(), $result[0]->name());
        $this->assertSame($request_group->name(), $result[1]->name());
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

        $request_group = new InformationGroup(
            name: 'Request',
            priority: 5,
        );

        $request_group
            ->add(new TextInfo('Method', 'GET'))
            ->add(new TextInfo('Path', '/'))
            ->add(new TextInfo('Query', 'search=something'));

        $git_group = new InformationGroup(
            name: 'Git',
            priority: 15,
        );

        $git_group
            ->add(new TextInfo('Branch', 'main'))
            ->add(new TextInfo('User', 'You'));

        $information = new InMemoryInformation();
        $information->add($group);
        $information->add($request_group);
        $information->add($git_group);

        $result = $information->get_information_by_priority();

        $this->assertSame($request_group->name(), $result[0]->name());
        $this->assertSame($request_group->priority(), $result[0]->priority());

        $this->assertSame($group->name(), $result[1]->name());
        $this->assertSame($group->priority(), $result[1]->priority());

        $this->assertSame($git_group->name(), $result[2]->name());
        $this->assertSame($git_group->priority(), $result[2]->priority());
    }
}
