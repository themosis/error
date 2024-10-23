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

final class Issue
{
    private function __construct(
        private Throwable $exception,
        private IssueDate $occuredAt,
    ) {
    }

    public static function fromException(Throwable $exception, ?DateTimeImmutable $occuredAt = null): self
    {
        return new self(
            exception: $exception,
            occuredAt: new IssueDate($occuredAt ?? new DateTimeImmutable('now')),
        );
    }

    public function message(): string
    {
        return $this->exception->getMessage();
    }

    public function date(string $format = DateTimeImmutable::W3C): string
    {
        return $this->occuredAt
            ->withFormat($format)
            ->toString();
    }

    public function level(): Level
    {
        if ($this->exception instanceof Levelable) {
            return $this->exception->level();
        }

        return Level::Error;
    }

    public function info(): ?InformationGroup
    {
        if ($this->exception instanceof AdditionalInformation) {
            return $this->exception->information();
        }

        return null;
    }

    public function exception(): Throwable
    {
        return $this->exception;
    }

    public function occuredAt(): IssueDate
    {
        return $this->occuredAt;
    }

    public function preview(): FilePreview
    {
        return new FilePreview(
            file: new File(
                filepath: $this->exception->getFile(),
                line: $this->exception->getLine(),
            ),
        );
    }
}
