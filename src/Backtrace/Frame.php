<?php

declare(strict_types=1);

namespace Themosis\Components\Error\Backtrace;

use Stringable;

final class Frame implements Stringable {
	private FrameFunction $function;
	private File $file;
	private ?object $object;
	private array $args;

	public function __construct( array $frame ) {
		$this->file = new File(
			filepath: $frame['file'] ?? null,
			line: $frame['line'] ?? null,
		);

		$this->function = isset( $frame['class'] )
			? new ClassFunction(
				class_name: $frame['class'],
				function_name: $frame['function'],
				type: $frame['type'],
			)
			: new PhpFunction(
				function_name: $frame['function'],
			);

		$this->object = $frame['object'] ?? null;
		$this->args   = $frame['args'] ?? [];
	}

	public function get_function(): FrameFunction {
		return $this->function;
	}

	public function get_file(): File {
		return $this->file;
	}

	public function get_object(): ?object {
		return $this->object;
	}

	public function get_args(): array {
		return $this->args;
	}

	public function __toString(): string {
		$elements = [
			$this->file,
			$this->function,
		];

		return implode( ' ', $elements );
	}

	/**
	 * This should be replaced by the "FrameTypes" mechanism...
	 * in order to let developer describe which frames to identify...
	 */
	public function is_php_core(): bool {
		return false;
	}

	public function is_wordpress(): bool {
		return false;
	}
}
