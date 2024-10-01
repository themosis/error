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

	public function render( Issue $issue ): void {
		$content = static function ( string $path, array $data = [] ) {
			extract( $data );

			return require $path;
		};

		$exception = $issue->exception();

		$content(
			$this->view_path,
			[
				'title'           => $issue->message(),
				'message'         => $issue->message(),
				'exception_class' => get_class( $exception ),
				'file'            => sprintf( '%s:%s', $exception->getFile(), $exception->getLine() ),
				'preview'         => fn ( Closure $preview_callback, Closure $line_callback ) => $this->render_preview( $issue->preview(), $preview_callback, $line_callback ),
				'frames'          => function ( Closure $frames_callback, Closure $frame_callback, Closure $tag_callback, Closure $preview_callback, Closure $line_callback ) {
					if ( empty( $this->backtrace->frames() ) ) {
						return;
					}

					$frames = array_map(
						fn ( Frame $frame ) => $frame_callback(
							function: htmlentities( (string) $frame->get_function() ),
							file: htmlentities( (string) $frame->get_file() ),
							tags: $this->render_tags( $frame, $tag_callback ),
							preview: $this->render_preview( new FilePreview( $frame->get_file() ), $preview_callback, $line_callback )
						),
						$this->backtrace->frames()
					);

					return $frames_callback( implode( PHP_EOL, $frames ) );
				},
			]
		);
	}

	private function render_tags( Frame $frame, callable $tag_callback ): string {
		$tags = array_map( static fn ( FrameTag $tag ) => $tag_callback( htmlentities( $tag->name() ) ), $frame->tags() );

		return implode( PHP_EOL, $tags );
	}

	private function render_preview( FilePreview $file, callable $preview_callback, callable $line_callback ): string {
		$lines = array_map(
			static fn ( string $line, int $number ) => $line_callback(
				class_name: $file->is_current_line( $number ) ? 'current-line' : '',
				length: $file->row_number_length(),
				number: $number,
				line: $line,
			),
			$file->get_lines(),
			array_keys( $file->get_lines() )
		);

		return $preview_callback( implode( PHP_EOL, $lines ) );
	}
}
