<?php

// SPDX-FileCopyrightText: 2024 Julien Lambé <julien@themosis.com>
//
// SPDX-License-Identifier: GPL-3.0-or-later

declare(strict_types=1);

namespace Themosis\Components\Error;

interface Information
{
    public function add(?InformationGroup $info): void;

    public function get_information_by_priority(): array;
}
