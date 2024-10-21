<?php

// SPDX-FileCopyrightText: 2024 Julien Lambé <julien@themosis.com>
//
// SPDX-License-Identifier: GPL-3.0-or-later

declare(strict_types=1);

namespace Themosis\Components\Error\Backtrace;

final class VendorFrameIdentifier implements FrameIdentifier
{
    public function __construct(
        private string $project_root_path,
    ) {
        $this->project_root_path = rtrim($this->project_root_path, '\/');
    }

    public function tag(): FrameTag
    {
        return new VendorFrameTag();
    }

    public function identify(Frame $frame): bool
    {
        $path = $frame->get_file()->path();

        if (empty($path)) {
            return false;
        }

        $relative_path = str_replace($this->project_root_path, '', $path);

        if (str_contains($relative_path, 'vendor')) {
            return true;
        }

        return false;
    }
}
