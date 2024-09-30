<?php

declare(strict_types=1);

namespace Themosis\Components\Error;

use Closure;
use Themosis\Components\Error\Backtrace\Backtrace;
use Themosis\Components\Error\Backtrace\FilePreview;
use Themosis\Components\Error\Backtrace\Frame;
use Themosis\Components\Error\Backtrace\FrameTag;

final class ExceptionHandlerHttpResponse {
    public function __construct(
        private string $view_path,
        private Backtrace $backtrace,
    ) {
	}

	public function render(Issue $issue): void {
        $content = static function (string $path, array $data = []) {
            extract($data);
        
            return require $path;
        };

        $exception = $issue->exception();

        $content($this->view_path, [
            'title' => $issue->message(),
            'message' => $issue->message(),
            'exception_class' => get_class($exception),
            'file' => sprintf('%s:%s', $exception->getFile(), $exception->getLine()),
            'preview' => function (Closure $preview_callback, Closure $line_callback) use ($issue) {
                return $preview_callback($this->render_preview_lines($issue->preview(), $line_callback));
            },
            'frames' => function (Closure $frames_callback) {
                return function (Closure $frame_callback) use ($frames_callback) {
                    return function (Closure $tag_callback) use ($frame_callback, $frames_callback) {
                        return function (Closure $compose) use ($frame_callback, $frames_callback, $tag_callback) {
                            if (empty($this->backtrace->frames())) {
                                return;
                            }

                            return $frames_callback(implode('', array_map(function (Frame $frame) use ($frame_callback, $tag_callback, $compose) {
                                $function = htmlentities((string) $frame->get_function());
                                $file = htmlentities((string) $frame->get_file());

                                $tags = array_map(static function (FrameTag $tag) use ($tag_callback) {
                                    return $tag_callback($tag->name());
                                }, $frame->tags());

                                $render_preview = function (Closure $preview_callback, Closure $line_cb) use ($frame) {
                                    return $preview_callback($this->render_preview_lines(
                                        preview: new FilePreview(
                                            file: $frame->get_file(),
                                        ),
                                        line_callback: $line_cb,));
                                };

                                return $frame_callback($function, $file, implode(PHP_EOL, $tags), $compose($render_preview));
                            }, $this->backtrace->frames())));
                        };
                    };
                };
            },
        ]);
	}

    private function render_preview_lines(FilePreview $preview, callable $line_callback): string
    {
        $lines = array_map(static function (string $line, int $number) use ($line_callback, $preview) {
            return $line_callback(
                $preview->is_current_line($number) ? 'current-line' : '',
                $preview->row_number_length(),
                $number,
                $line,
            );
        }, $preview->get_lines(), array_keys($preview->get_lines()));

        return implode(PHP_EOL, $lines);
    }
}
