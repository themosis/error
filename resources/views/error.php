<?php

use Themosis\Components\Error\Backtrace\AppFrameTag;
use Themosis\Components\Error\Backtrace\Backtrace;
use Themosis\Components\Error\Backtrace\CustomFrameIdentifier;
use Themosis\Components\Error\Backtrace\Frame;
use Themosis\Components\Error\Backtrace\InMemoryFrameIdentifiers;
use Themosis\Components\Error\ExceptionHandler;
use Themosis\Components\Error\ExceptionHandlerHttpResponse;
use Themosis\Components\Error\InMemoryIssues;
use Themosis\Components\Error\InMemoryReporters;
use Themosis\Components\Error\Issue;
use Themosis\Components\Error\Reporters\CallbackReporter;
use Themosis\Components\Error\Reporters\Conditions\AlwaysReport;
use Themosis\Components\Error\ReportHandler;

require_once __DIR__ .  '/../../vendor/autoload.php';

$identifiers = new InMemoryFrameIdentifiers();
$backtrace = new Backtrace($identifiers);

$reporters = new InMemoryReporters();
$reporters->add(
    condition: new AlwaysReport(),
    reporter: new CallbackReporter(static function (Issue $issue) {
        $identifiers = new InMemoryFrameIdentifiers();
        $identifiers->add(new CustomFrameIdentifier(
            tag: new AppFrameTag(),
            identifier: static function (Frame $frame) {
                if (str_contains($frame->get_file()->path(), 'nested-error')) {
                    return true;
                }

                return false;
            },
        ));

        $backtrace = new Backtrace(
            frame_identifiers: $identifiers,
        );

        $backtrace->capture_exception($issue->exception());

        (new ExceptionHandlerHttpResponse(
            view_path: __DIR__ . '/exception.php',
            backtrace: $backtrace,
        ))->render($issue);
    }),
);

$reportHandler = new ReportHandler(
    reporters: $reporters,
    issues: new InMemoryIssues(),
);

set_exception_handler(new ExceptionHandler($reportHandler));

/*
$reportHandler->capture(
    issue: Issue::from_exception(
        new Exception('There was an error when calling the checkout client.', previous: new RuntimeException('Oops!'))
    )
);

$reportHandler->publish();
*/
include __DIR__ .'/nested-error.php';

