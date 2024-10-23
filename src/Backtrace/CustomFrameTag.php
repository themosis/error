<?php

// SPDX-FileCopyrightText: 2024 Julien Lambé <julien@themosis.com>
//
// SPDX-License-Identifier: GPL-3.0-or-later

declare(strict_types=1);

namespace Themosis\Components\Error\Backtrace;

use Themosis\Components\Error\Exceptions\InvalidFrameTagArgument;

final class CustomFrameTag extends AbstractFrameTag
{
    public function __construct(
        private string $slug,
        private string $name,
    ) {
        if ($this->slugHasInvalidFormat($this->slug)) {
            throw new InvalidFrameTagArgument(
                message: sprintf(
                    'Frame tag slug "%s" is not valid. Use only lowercase letters and underscores.',
                    $this->slug
                ),
            );
        }

        if (empty($this->name)) {
            throw new InvalidFrameTagArgument(
                message: sprintf('Frame tag name "%s" is not valid. Avoid empty tag names.', $this->name),
            );
        }
    }

    private function slugHasInvalidFormat(string $slug): bool
    {
        if (empty($slug)) {
            return true;
        }

        $isMatching = preg_match(
            pattern: '/[^a-z_]+/',
            subject: $slug,
        );

        if (1 === $isMatching) {
            return true;
        }

        return false;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function slug(): string
    {
        return $this->slug;
    }
}
