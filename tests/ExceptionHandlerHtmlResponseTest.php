<?php

// SPDX-FileCopyrightText: 2024 Julien Lambé <julien@themosis.com>
//
// SPDX-License-Identifier: GPL-3.0-or-later

declare(strict_types=1);

namespace Themosis\Components\Error\Tests;

use Exception;
use PHPUnit\Framework\Attributes\Test;
use RuntimeException;
use Themosis\Components\Error\Backtrace\Backtrace;
use Themosis\Components\Error\Backtrace\InMemoryFrameIdentifiers;
use Themosis\Components\Error\ExceptionalIssue;
use Themosis\Components\Error\ExceptionHandlerHtmlResponse;
use Themosis\Components\Error\InformationGroup;
use Themosis\Components\Error\InMemoryInformation;
use Themosis\Components\Error\TextInfo;

final class ExceptionHandlerHtmlResponseTest extends TestCase
{
    #[Test]
    public function it_can_render_exception_without_information(): void
    {
        $backtrace = new Backtrace(
            frameIdentifiers: new InMemoryFrameIdentifiers(),
        );

        $response = new ExceptionHandlerHtmlResponse(
            backtrace: $backtrace,
            information: new InMemoryInformation(),
        );

        $issue = ExceptionalIssue::create(new RuntimeException('Something went wrong.'));

        ob_start();
            $response->send($issue);
        $stdout = ob_get_clean();

        $this->assertTrue(str_contains($stdout, 'Issue'));
        $this->assertTrue(str_contains($stdout, 'Something went wrong.'));
    }

    #[Test]
    public function it_can_render_exception_with_information(): void
    {
        $backtrace = new Backtrace(
            frameIdentifiers: new InMemoryFrameIdentifiers(),
        );

        $information = new InMemoryInformation();

        $information->add((new InformationGroup(
            name: 'General',
        ))
            ->add(new TextInfo('Version', '1.0.5'))
            ->add(new TextInfo('Environment', 'staging')));

        $response = new ExceptionHandlerHtmlResponse(
            backtrace: $backtrace,
            information: $information,
        );

        $issue = ExceptionalIssue::create(new FakeException('Oops, we have a problem!'));

        ob_start();
            $response->send($issue);
        $stdout = ob_get_clean();

        $this->assertTrue(str_contains($stdout, 'General'));
        $this->assertTrue(str_contains($stdout, 'Version'));
        $this->assertTrue(str_contains($stdout, '1.0.5'));
        $this->assertTrue(str_contains($stdout, 'Environment'));
        $this->assertTrue(str_contains($stdout, 'staging'));
    }

    #[Test]
    public function it_can_render_exception_with_their_ancestors(): void
    {
        $backtrace = new Backtrace(
            frameIdentifiers: new InMemoryFrameIdentifiers(),
        );

        $response = new ExceptionHandlerHtmlResponse(
            backtrace: $backtrace,
            information: new InMemoryInformation(),
        );

        $stdout = '';

        try {
            throw new RuntimeException('First exception');
        } catch (RuntimeException $firstException) {
            $secondException = new Exception('Second exception', previous: $firstException);

            $issue = ExceptionalIssue::create($secondException);

            ob_start();
                $response->send($issue);
            ;
            $stdout = ob_get_clean();
        }

        $this->assertTrue(str_contains($stdout, 'First exception'));
        $this->assertTrue(str_contains($stdout, 'Second exception'));
    }
}
