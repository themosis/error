<?php

declare(strict_types=1);

namespace Themosis\Components\Error;

final class ReportHandler {
	public function __construct(
		private Reporters $reporters,
		private Issues $issues,
	) {
	}

	public function capture( Issue $issue ): static {
		$this->issues->add( $issue );

		return $this;
	}

	public function publish(): void {
		$call_reporters_on_captured_issues = static function ( Issues $issues ) {
			return static function ( Reporters $reporters ) use ( $issues ) {
				array_map(
					static function ( Issue $issue ) use ( $reporters ) {
						array_map(
							static function ( Reporter $reporter ) use ( $issue ) {
								$reporter->report( $issue );
							},
							$reporters->all(),
						);
					},
					$issues->all(),
				);
			};
		};

		$call_reporters_on_captured_issues( $this->issues )( $this->reporters );
	}
}
