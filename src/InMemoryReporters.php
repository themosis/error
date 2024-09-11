<?php

declare(strict_types=1);

namespace Themosis\Components\Error;

use Closure;

final class InMemoryReporters implements Reporters {
	/**
	 * @var array<int, array<int, Closure>>
	 */
	private array $reporters = [];

	public function add( ReportCondition $condition, Reporter $reporter ): void {
		$resolver = static function ( mixed $value ): Closure {
			return static function () use ( $value ): mixed {
				return $value;
			};
		};

		$this->reporters[] = [ $resolver( $condition ), $resolver( $reporter ) ];
	}

	/**
	 * @return array<int, Reporter>
	 */
	public function get_allowed_reporters( Issue $issue ): array {
		return array_reduce(
			$this->reporters,
			function ( array $carry, array $value ) use ( $issue ) {
				[$condition, $reporter] = $value;

				if ( $condition()->can( $issue ) ) {
					$carry[] = $reporter();
				}

				return $carry;
			},
			[]
		);
	}
}
