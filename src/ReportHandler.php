<?php

declare(strict_types=1);

namespace Themosis\Components\Error;

use DateTimeImmutable;
use Throwable;

final class ReportHandler {
	/**
	 * @var array<int, Issue>
	 */
	private array $captured_issues = [];

	public function __construct(
		private Reporters $reporters,
	) {
	}

	public function report( Throwable $exception, ?DateTimeImmutable $occured_at = null ): void {
		$this->captured_issues[] = Issue::from_exception( $exception, $occured_at );
	}

	public function release(): void {
		$call_reporters_on_captured_issues = static function ( array $issues ) {
			return static function ( array $reporters ) use ( $issues ) {
				array_map(
					static function ( Issue $issue ) use ( $reporters ) {
						array_map(
							static function ( Reporter $reporter ) use ( $issue ) {
								$reporter->report( $issue );
							},
							$reporters
						);
					},
					$issues
				);
			};
		};

		$call_reporters_on_captured_issues( $this->captured_issues )( $this->reporters->all() );
	}
}
