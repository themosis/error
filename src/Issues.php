<?php

// SPDX-FileCopyrightText: 2024 Julien Lambé <julien@themosis.com>
//
// SPDX-License-Identifier: GPL-3.0-or-later

declare(strict_types=1);

namespace Themosis\Components\Error;

interface Issues
{
    public function add(Issue $issue): void;

    /**
     * @return array<int, Issue>
     */
    public function all(): array;
}
