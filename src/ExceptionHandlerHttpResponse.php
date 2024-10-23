<?php

// SPDX-FileCopyrightText: 2024 Julien Lambé <julien@themosis.com>
//
// SPDX-License-Identifier: GPL-3.0-or-later

declare(strict_types=1);

namespace Themosis\Components\Error;

use Closure;
use Themosis\Components\Error\Backtrace\Backtrace;
use Themosis\Components\Error\Backtrace\FilePreview;
use Themosis\Components\Error\Backtrace\FilePreviewLine;
use Themosis\Components\Error\Backtrace\Frame;
use Themosis\Components\Error\Backtrace\FrameTag;

final class ExceptionHandlerHttpResponse
{
    public function __construct(
        private string $viewPath,
        private Backtrace $backtrace,
        private Information $information,
    ) {
    }

    public function render(Issue $issue): void
    {
        $content = static function (string $path, array $data = []) {
            // phpcs:ignore
			extract( $data );

            return require $path;
        };

        $exception = $issue->exception();

        $content(
            $this->viewPath,
            [
                'title' => $issue->message(),
                'message' => $issue->message(),
                'exceptionClass' => get_class($exception),
                'file' => sprintf('%s:%s', $exception->getFile(), $exception->getLine()),
                'preview' => function (Closure $previewCallback, Closure $lineCallback) use ($issue) {
                    return $this->renderPreview($issue->preview(), $previewCallback, $lineCallback);
                },
                'frames' => function (
                    Closure $framesCallback,
                    Closure $frameCallback,
                    Closure $tagCallback,
                    Closure $previewCallback,
                    Closure $lineCallback,
                ) {
                    if (empty($this->backtrace->frames())) {
                        return;
                    }

                    $frames = array_map(
                        fn (Frame $frame) => $frameCallback(
                            function: htmlentities((string) $frame->getFunction()),
                            file: htmlentities((string) $frame->getFile()),
                            tags: $this->renderTags($frame, $tagCallback),
                            preview: $this->renderPreview(
                                new FilePreview($frame->getFile()),
                                $previewCallback,
                                $lineCallback
                            ),
                        ),
                        $this->backtrace->frames()
                    );

                    return $framesCallback(implode(PHP_EOL, $frames));
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
                        [ $navigationCallback(id: 'issue', title: 'Issue') ]
                    );

                    return implode(PHP_EOL, $items);
                },
            ]
        );
    }

    private function renderTags(Frame $frame, callable $tagCallback): string
    {
        $tags = array_map(static fn (FrameTag $tag) => $tagCallback(htmlentities($tag->name())), $frame->tags());

        return implode(PHP_EOL, $tags);
    }

    private function renderPreview(FilePreview $file, callable $previewCallback, callable $lineCallback): string
    {
        $lines = array_map(
            static fn (FilePreviewLine $line) => $lineCallback(
                className: $file->isCurrentLine($line->number()) ? 'current-line' : '',
                length: $file->rowNumberLength(),
                number: $line->number(),
                line: $line->content(),
            ),
            $file->getLines(),
        );

        return $previewCallback(implode(PHP_EOL, $lines));
    }
}
