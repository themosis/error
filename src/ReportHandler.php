<?php

// SPDX-FileCopyrightText: 2024 Julien Lambé <julien@themosis.com>
//
// SPDX-License-Identifier: GPL-3.0-or-later

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
		$should_stop = function ( ?int $code ): bool {
			return ! is_null( $code ) && 0 < (int) $code;
		};

		array_reduce(
			$this->issues->all(),
			static function ( callable $reporters_for, Issue $issue ) use ( $should_stop ) {
				foreach ( $reporters_for( $issue ) as $reporter ) {
					if ( $should_stop( $reporter->report( $issue ) ) ) {
						break;
					}
				}

				return $reporters_for;
			},
			$this->reporters->get_allowed_reporters( ... )
		);
	}
}
