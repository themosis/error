<?php

// SPDX-FileCopyrightText: 2024 Julien Lambé <julien@themosis.com>
//
// SPDX-License-Identifier: GPL-3.0-or-later

declare(strict_types=1);

namespace Themosis\Components\Error;

final class AdditionalInformation {
	private array $info = [];

	public function add( string $key, string $value ): static {
		$this->info[ $key ] = $value;

		return $this;
	}

	public function as_array(): array {
		return $this->info;
	}
}
