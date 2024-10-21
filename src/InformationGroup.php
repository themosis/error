<?php

// SPDX-FileCopyrightText: 2024 Julien Lambé <julien@themosis.com>
//
// SPDX-License-Identifier: GPL-3.0-or-later

declare(strict_types=1);

namespace Themosis\Components\Error;

final class InformationGroup
{
    /**
     * @var array<int,Info> $information
     */
    private array $information = [];

    public function __construct(
        private string $name,
        private int $priority = 10,
    ) {
    }

    public function name(): string
    {
        return $this->name;
    }

    public function slug(): string
    {
        return strtolower(str_replace(' ', '-', $this->name));
    }

    public function title(): string
    {
        return ucfirst($this->name);
    }

    public function priority(): int
    {
        return $this->priority;
    }

    public function add(Info $info): self
    {
        $this->information[] = $info;

        return $this;
    }

    /**
     * @return array<int,Info>
     */
    public function get_information(): array
    {
        return $this->information;
    }
}
