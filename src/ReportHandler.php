<?php

// SPDX-FileCopyrightText: 2024 Julien Lambé <julien@themosis.com>
//
// SPDX-License-Identifier: GPL-3.0-or-later

declare(strict_types=1);

namespace Themosis\Components\Error;

use Closure;

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
		$should_stop = static function ( Reporter $reporter ): Closure {
			return static function ( Issue $issue ) use ( $reporter ): bool {
				return $reporter instanceof HaltReporter && $reporter->stop( $issue );
			};
		};

		array_reduce(
			$this->issues->all(),
			static function ( callable $reporters_for, Issue $issue ) use ( $should_stop ) {
				foreach ( $reporters_for( $issue ) as $reporter ) {
					/** @var Reporter $reporter */
					$reporter->report( $issue );

					if ( $should_stop( $reporter )( $issue ) ) {
						break;
					}
				}

				return $reporters_for;
			},
			$this->reporters->get_allowed_reporters( ... )
		);
	}
}
