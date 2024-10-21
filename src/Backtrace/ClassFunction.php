<?php

// SPDX-FileCopyrightText: 2024 Julien Lambé <julien@themosis.com>
//
// SPDX-License-Identifier: GPL-3.0-or-later

declare(strict_types=1);

namespace Themosis\Components\Error\Backtrace;

final class ClassFunction implements FrameClassFunction
{
    public function __construct(
        private string $class_name,
        private string $function_name,
        private string $type,
    ) {
    }

    public function get_class(): string
    {
        return $this->class_name;
    }

    public function __toString(): string
    {
        return sprintf('%s%s%s()', $this->class_name, $this->type, $this->function_name);
    }
}
