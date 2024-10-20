<?php

// SPDX-FileCopyrightText: 2024 Julien Lambé <julien@themosis.com>
//
// SPDX-License-Identifier: GPL-3.0-or-later

declare(strict_types=1);

namespace Themosis\Components\Error;

final class InMemoryInformation implements Information {
	/**
	 * @var array<int,InformationGroup>
	 */
	private array $information = [];

	public function add( ?InformationGroup $info ): void {
		if ( ! $info ) {
			return;
		}

		$this->information[] = $info;
	}

	/**
	 * @return array<int,InformationGroup>
	 */
	public function get_information_by_priority(): array {
		$information = $this->information;

		usort(
			$information,
			static function ( InformationGroup $a, InformationGroup $b ) {
				return $a->priority() <=> $b->priority();
			}
		);

		return $information;
	}
}
