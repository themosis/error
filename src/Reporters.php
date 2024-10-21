<?php

// SPDX-FileCopyrightText: 2024 Julien Lambé <julien@themosis.com>
//
// SPDX-License-Identifier: GPL-3.0-or-later

declare(strict_types=1);

namespace Themosis\Components\Error;

interface Reporters
{
    public function add(ReportCondition $condition, Reporter $reporter): void;

    public function get_allowed_reporters(Issue $issue): array;
}
