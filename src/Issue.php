<?php

// SPDX-FileCopyrightText: 2024 Julien Lambé <julien@themosis.com>
//
// SPDX-License-Identifier: GPL-3.0-or-later

declare(strict_types=1);

namespace Themosis\Components\Error;

use Throwable;

interface Issue
{
    public function message(): string;

    public function date(): IssueDate;

    public function level(): Level;

    public function exception(): Throwable;
}
