<?php

declare(strict_types=1);

namespace Themosis\Components\Error;

use DateTimeImmutable;
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

    public function message(): string
    {
        return $this->exception->getMessage();
    }
    
    public function date(string $format = DateTimeImmutable::W3C): string
    {
        return $this->occured_at
            ->with_format( $format )
            ->as_string();
    }

	public function exception(): Throwable {
		return $this->exception;
	}

	public function occured_at(): IssueDate {
		return $this->occured_at;
	}
}
