<?php

declare(strict_types=1);

namespace Themosis\Components\Error;

use DateTimeImmutable;

final class IssueDate {
	private string $format = DateTimeImmutable::W3C;

	public function __construct(
		private DateTimeImmutable $datetime,
	) {}

	public function with_format( string $format ): static {
		$this->format = $format;

		return $this;
	}

	public function as_string(): string {
		return $this->datetime->format( $this->format );
	}
}
