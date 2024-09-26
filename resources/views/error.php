<?php

use Themosis\Components\Error\Backtrace\AppFrameTag;
use Themosis\Components\Error\Backtrace\Backtrace;
use Themosis\Components\Error\Backtrace\CustomFrameIdentifier;
use Themosis\Components\Error\Backtrace\Frame;
use Themosis\Components\Error\Backtrace\FrameTag;
use Themosis\Components\Error\Backtrace\InMemoryFrameIdentifiers;
use Themosis\Components\Error\ExceptionHandler;
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
        $content = static function (array $data = []) {
            extract($data);
        
            return require __DIR__ . '/exception.php';
        };

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

        $html = $content([
            'title' => $issue->message(),
            'message' => $issue->message(),
            'exception_class' => get_class($issue->exception()),
            'file' => sprintf('%s:%s', $issue->exception()->getFile(), $issue->exception()->getLine()),
            'preview' => $issue->preview(),
            'frames' => static function ($wrapper_callback) use ($backtrace) {
                return static function ($nested_callback) use ($backtrace, $wrapper_callback) {
                    return static function ($tag_callback) use ($backtrace, $nested_callback, $wrapper_callback) {
                        if (empty($backtrace->frames())) {
                            return;
                        }

                        echo $wrapper_callback(implode('', array_map(static function (Frame $frame) use ($nested_callback, $tag_callback) {
                            $function = htmlentities($frame->get_function());
                            $file = htmlentities($frame->get_file());


                            $tags = array_map(static function (FrameTag $tag) use ($tag_callback) {
                                return $tag_callback($tag->name());
                            }, $frame->tags());

                            return $nested_callback($function, $file, implode(PHP_EOL, $tags));
                        }, $backtrace->frames())));
                    };
                };
            },
        ]);

        return $html;
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

