<?php

// SPDX-FileCopyrightText: 2024 Julien Lambé <julien@themosis.com>
//
// SPDX-License-Identifier: GPL-3.0-or-later

declare(strict_types=1);

namespace Themosis\Components\Error\Backtrace;

final class VendorFrameIdentifier implements FrameIdentifier
{
    public function __construct(
        private string $projectRootPath,
    ) {
        $this->projectRootPath = rtrim($this->projectRootPath, '\/');
    }

    public function tag(): FrameTag
    {
        return new VendorFrameTag();
    }

    public function identify(Frame $frame): bool
    {
        $path = $frame->getFile()->path();

        if (empty($path)) {
            return false;
        }

        $relativePath = str_replace($this->projectRootPath, '', $path);

        if (str_contains($relativePath, 'vendor')) {
            return true;
        }

        return false;
    }
}
