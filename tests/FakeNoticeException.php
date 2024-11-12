<?php

// SPDX-FileCopyrightText: 2024 Julien Lambé <julien@themosis.com>
//
// SPDX-License-Identifier: GPL-3.0-or-later

declare(strict_types=1);

namespace Themosis\Components\Error\Tests;

use Exception;
use Themosis\Components\Error\Level;
use Themosis\Components\Error\Levelable;

final class FakeNoticeException extends Exception implements Levelable
{
    public function level(): Level
    {
        return Level::Notice;
    }
}
