<?php

// SPDX-FileCopyrightText: 2024 Julien Lambé <julien@themosis.com>
//
// SPDX-License-Identifier: GPL-3.0-or-later

declare(strict_types=1);

namespace Themosis\Components\Error;

use DateTimeImmutable;
use Themosis\Components\Error\Backtrace\File;
use Themosis\Components\Error\Backtrace\FilePreview;
use Throwable;

final class Issue {
	private function __construct(
		private Throwable $exception,
		private IssueDate $occured_at,
	) {
	}

	public static function from_exception( Throwable $exception, ?DateTimeImmutable $occured_at = null ): self {
		return new self(
			exception: $exception,
			occured_at: new IssueDate( $occured_at ?? new DateTimeImmutable( 'now' ) ),
		);
	}

	public function message(): string {
		return $this->exception->getMessage();
	}

	public function date( string $format = DateTimeImmutable::W3C ): string {
		return $this->occured_at
			->with_format( $format )
			->as_string();
	}

	public function level(): Level {
		if ( $this->exception instanceof Levelable ) {
			return $this->exception->level();
		}

		return Level::Error;
	}

	public function info(): ?InformationGroup {
		if ( $this->exception instanceof AdditionalInformation ) {
			return $this->exception->information();
		}

		return null;
	}

	public function exception(): Throwable {
		return $this->exception;
	}

	public function occured_at(): IssueDate {
		return $this->occured_at;
	}

	public function preview(): FilePreview {
		return new FilePreview(
			file: new File(
				filepath: $this->exception->getFile(),
				line: $this->exception->getLine(),
			),
		);
	}
}
