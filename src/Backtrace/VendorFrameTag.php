<?php

// SPDX-FileCopyrightText: 2024 Julien Lambé <julien@themosis.com>
//
// SPDX-License-Identifier: GPL-3.0-or-later

declare(strict_types=1);

namespace Themosis\Components\Error\Backtrace;

final class VendorFrameTag extends AbstractFrameTag
{
    public function slug(): string
    {
        return 'vendor';
    }

    public function name(): string
    {
        return 'Vendor';
    }
}
