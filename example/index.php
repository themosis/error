<?php

use Themosis\Components\Error\AdditionalInformation;
use Themosis\Components\Error\Backtrace\AppFrameTag;
use Themosis\Components\Error\Backtrace\Backtrace;
use Themosis\Components\Error\Backtrace\CustomFrameIdentifier;
use Themosis\Components\Error\Backtrace\Frame;
use Themosis\Components\Error\Backtrace\InMemoryFrameIdentifiers;
use Themosis\Components\Error\ErrorHandler;
use Themosis\Components\Error\ExceptionHandler;
use Themosis\Components\Error\ExceptionHandlerHtmlResponse;
use Themosis\Components\Error\InformationGroup;
use Themosis\Components\Error\InMemoryInformation;
use Themosis\Components\Error\InMemoryIssues;
use Themosis\Components\Error\InMemoryReporters;
use Themosis\Components\Error\Issue;
use Themosis\Components\Error\Reporters\CallbackReporter;
use Themosis\Components\Error\Reporters\Conditions\AlwaysReport;
use Themosis\Components\Error\ReportHandler;
use Themosis\Components\Error\TextInfo;

require __DIR__ . '/../vendor/autoload.php';

class InformativeException extends Exception implements AdditionalInformation
{
    public function information(): InformationGroup {
        $information = new InformationGroup(
            name: 'Order',
        );

        $information->add(new TextInfo('Order #', 'ORDER-43242'));

        return $information;
    }
}

$reporters = new InMemoryReporters();
$reporters->add(
    condition: new AlwaysReport(),
    reporter: new CallbackReporter(static function (Issue $issue) {
        $identifiers = new InMemoryFrameIdentifiers();
        $identifiers->add(new CustomFrameIdentifier(
            tag: new AppFrameTag(),
            identifier: static function (Frame $frame) {
                return str_contains($frame->getFile()?->path() ?? '', 'nested-error');
            },
        ));

        $backtrace  = new Backtrace(
            frameIdentifiers: $identifiers,
        );

        (new ExceptionHandlerHtmlResponse(
            backtrace: $backtrace,
            information: new InMemoryInformation(),
        ))->send($issue);
    }),
);

$reportHandler = new ReportHandler(
    reporters: $reporters,
    issues: new InMemoryIssues(),
);

set_error_handler(new ErrorHandler($reportHandler));
set_exception_handler(new ExceptionHandler($reportHandler));

//trigger_error("Something went wrong", E_USER_ERROR);
include __DIR__ . '/nested-error.php';
