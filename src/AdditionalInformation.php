<?php

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
