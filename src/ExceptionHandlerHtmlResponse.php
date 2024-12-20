<?php

// SPDX-FileCopyrightText: 2024 Julien Lambé <julien@themosis.com>
//
// SPDX-License-Identifier: GPL-3.0-or-later

declare(strict_types=1);

namespace Themosis\Components\Error;

use Closure;
use Themosis\Components\Error\Backtrace\Backtrace;
use Themosis\Components\Error\Backtrace\File;
use Themosis\Components\Error\Backtrace\FilePreview;
use Themosis\Components\Error\Backtrace\FilePreviewLine;
use Themosis\Components\Error\Backtrace\Frame;
use Themosis\Components\Error\Backtrace\FrameTag;

final class ExceptionHandlerHtmlResponse
{
    private string $viewPath;

    public function __construct(
        private Backtrace $backtrace,
        private Information $information,
    ) {
        $this->viewPath = realpath(__DIR__ . '/../resources/views/exception.php');
    }

    public function send(Issue $issue): void
    {
        $content = static function (string $path, array $data = []) {
            // phpcs:ignore
            extract($data);

            return require $path;
        };

        $prepareInformation = function (array $stack): void {
            array_map(function (Issue $issue): void {
                if ($info = $issue->info()) {
                    $this->information->add($info);
                }
            }, $stack);
        };

        $prepareInformation($stack = $this->getIssueStack($issue));

        $content(
            $this->viewPath,
            [
                'title' => $issue->message(),
                'issues' => function (
                    Closure $issueCallback,
                    Closure $previewCallback,
                    Closure $lineCallback,
                    Closure $framesCallback,
                    Closure $frameCallback,
                    Closure $tagCallback
                ) use ($stack) {
                    $issues = array_map(
                        function (Issue $stackIssue) use (
                            $issueCallback,
                            $previewCallback,
                            $lineCallback,
                            $framesCallback,
                            $frameCallback,
                            $tagCallback
                        ) {
                            $exception = $stackIssue->exception();

                            return $issueCallback(
                                exceptionClass: get_class($exception),
                                message: $stackIssue->message(),
                                file: sprintf('%s:%s', $exception->getFile(), $exception->getLine()),
                                preview: $this->renderPreview(
                                    file: new File(
                                        filepath: $exception->getFile(),
                                        line: $exception->getLine(),
                                    ),
                                    previewCallback: $previewCallback,
                                    lineCallback: $lineCallback,
                                ),
                                backtrace: $this->renderBacktrace(
                                    $stackIssue,
                                    $framesCallback,
                                    $frameCallback,
                                    $tagCallback,
                                    $previewCallback,
                                    $lineCallback
                                ),
                            );
                        },
                        $stack
                    );

                    return implode(PHP_EOL, $issues);
                },
                'information' => function (
                    Closure $informationCallback,
                    Closure $infogroupCallback,
                    Closure $infoCallback
                ) {
                    $information = array_map(
                        static function (InformationGroup $group) use ($infogroupCallback, $infoCallback) {
                            $infos = array_map(
                                static function (Info $info) use ($infoCallback) {
                                    return $infoCallback(
                                        label: $info->name(),
                                        value: $info->value(),
                                    );
                                },
                                $group->getInformation()
                            );

                            return $infogroupCallback(
                                slug: $group->slug(),
                                title: $group->title(),
                                infos: implode(PHP_EOL, $infos),
                            );
                        },
                        $this->information->getInformationByPriority(),
                    );

                    return $informationCallback(implode(PHP_EOL, $information));
                },
                'navigation' => function (Closure $navigationCallback) {
                    $items = array_reduce(
                        $this->information->getInformationByPriority(),
                        static function (array $carry, InformationGroup $item) use ($navigationCallback) {
                            $carry[] = $navigationCallback(
                                id: $item->slug(),
                                title: $item->title(),
                            );

                            return $carry;
                        },
                        [$navigationCallback(id: 'issue', title: 'Issue')]
                    );

                    return implode(PHP_EOL, $items);
                },
            ]
        );
    }

    private function renderBacktrace(
        Issue $issue,
        Closure $framesCallback,
        Closure $frameCallback,
        Closure $tagCallback,
        Closure $previewCallback,
        Closure $lineCallback
    ): string {
        $this->backtrace->captureException($issue->exception());

        $frames = array_map(
            function (Frame $frame) use ($frameCallback, $tagCallback, $previewCallback, $lineCallback) {
                $preview = '';

                if ($file = $frame->getFile()) {
                    $preview = $this->renderPreview(
                        $file,
                        $previewCallback,
                        $lineCallback,
                    );
                }

                return $frameCallback(
                    function: htmlentities((string) $frame->getFunction()),
                    file: htmlentities((string) ($frame->getFile() ?: $frame->getFunction()->getName() . '()')),
                    tags: $this->renderTags($frame, $tagCallback),
                    preview: $preview,
                );
            },
            $this->backtrace->frames()
        );

        return $framesCallback(implode(PHP_EOL, $frames));
    }

    private function renderTags(Frame $frame, callable $tagCallback): string
    {
        $tags = array_map(static fn(FrameTag $tag) => $tagCallback(htmlentities($tag->name())), $frame->tags());

        return implode(PHP_EOL, $tags);
    }

    private function renderPreview(File $file, callable $previewCallback, callable $lineCallback): string
    {
        $preview = new FilePreview($file);

        $lines = array_map(
            static fn(FilePreviewLine $line) => $lineCallback(
                className: $preview->isCurrentLine($line->number()) ? 'current-line' : '',
                length: $preview->rowNumberLength(),
                number: $line->number(),
                line: $line->content(),
            ),
            $preview->getLines(),
        );

        return $previewCallback(implode(PHP_EOL, $lines));
    }

    private function getIssueStack(Issue $issue): array
    {
        $issues = [];

        return $this->getIssueFromException($issue, $issues);
    }

    private function getIssueFromException(Issue $issue, array &$issues): array
    {
        $issues[] = $issue;

        if ($previous = $issue->exception()->getPrevious()) {
            return $this->getIssueFromException(
                issue: ExceptionalIssue::create($previous),
                issues: $issues
            );
        }

        return $issues;
    }
}
